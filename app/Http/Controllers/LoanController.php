<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    // Listar todos os empréstimos
    public function index()
    {
        $loans = Loan::with(['book', 'user'])->get();
        return response()->json($loans);
    }

    // Registrar um novo empréstimo
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after:loan_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Obter o livro com bloqueio para atualização
            $book = Book::lockForUpdate()->find($request->book_id);

            if (!$book) {
                DB::rollBack();
                return response()->json(['error' => 'Livro não encontrado.'], 404);
            }

            // Verificar se o livro já está emprestado
            if ($book->is_loaned) {
                DB::rollBack();
                return response()->json(['error' => 'Este livro já está emprestado.'], 422);
            }

            // Registrar o empréstimo
            $loan = Loan::create([
                'user_id' => $request->user_id,
                'book_id' => $request->book_id,
                'loan_date' => $request->loan_date,
                'due_date' => $request->due_date,
                'return_date' => null,
            ]);

            // Atualizar o status do livro para indicar que está emprestado
            $book->update(['is_loaned' => true]);

            // Confirmar a transação
            DB::commit();

            return response()->json($loan->load('user', 'book'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar empréstimo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao registrar empréstimo.'], 500);
        }
    }

    // Devolver um livro
    public function returnBook(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do empréstimo não fornecido'], 400);
        }

        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['error' => 'Empréstimo não encontrado'], 404);
        }

        if ($loan->return_date) {
            return response()->json(['error' => 'Este livro já foi devolvido.'], 400);
        }

        // Registrar a data de devolução como a data atual
        $loan->update(['return_date' => now()]);

        // Atualizar o status do livro para indicar que não está mais emprestado
        $loan->book->update(['is_loaned' => false]);

        return response()->json(['message' => 'Livro devolvido com sucesso', 'loan' => $loan]);
    }
}
