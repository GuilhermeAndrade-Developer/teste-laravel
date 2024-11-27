<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    // Listar todos os livros
    public function index()
    {
        $books = Book::with('authors')->get();
        return response()->json($books);
    }

    // Criar um novo livro
    public function store(Request $request)
    {
        // Validação dos dados do livro
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'publication_year' => 'required|integer|digits:4',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Criação do livro
        try {
            $book = Book::create($request->only(['title', 'publication_year']));

            // Associar autores ao livro
            if ($request->has('authors')) {
                $book->authors()->attach($request->authors);
            }

            return response()->json($book->load('authors'), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar o livro. Entre em contato com o suporte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Mostrar um livro
    public function show(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do livro não fornecido'], 400);
        }

        $book = Book::with('authors')->find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        return response()->json($book);
    }

    // Atualizar um livro
    public function update(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do livro não fornecido'], 400);
        }

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'publication_year' => 'sometimes|integer|digits:4',
            'authors' => 'sometimes|array',
            'authors.*' => 'exists:authors,id',
        ]);

        $book->update($request->only(['title', 'publication_year']));

        if ($request->has('authors')) {
            $book->authors()->sync($request->authors);
        }

        return response()->json($book->load('authors'));
    }

    // Deletar um livro
    public function destroy(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do livro não fornecido'], 400);
        }

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        $book->delete();
        return response()->json(['message' => 'Livro deletado com sucesso']);
    }
}
