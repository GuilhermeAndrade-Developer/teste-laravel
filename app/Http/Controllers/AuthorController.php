<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    // Listar todos os autores
    public function index()
    {
        $authors = Author::all();
        return response()->json($authors);
    }

    // Criar um novo autor
    public function store(Request $request)
    {
        // Validação dos dados do usuário
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Erro de validação, verifique os campos fornecidos.'
            ], 422);
        }

        // Criação do autor
        $author = Author::create($request->all());
        return response()->json($author, 201);
    }

    // Mostrar um autor específico
    public function show(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do autor não fornecido'], 400);
        }

        $author = Author::find($id);

        if (!$author) {
            return response()->json(['error' => 'Autor não encontrado'], 404);
        }

        return response()->json($author);
    }

    // Atualizar um autor
    public function update(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do autor não fornecido'], 400);
        }

        $author = Author::find($id);

        if (!$author) {
            return response()->json(['error' => 'Autor não encontrado'], 404);
        }

        // Validação parcial, onde os campos são opcionais
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
        ]);

        // Atualiza os campos fornecidos na solicitação
        $author->update($request->only(['name', 'birth_date']));

        return response()->json($author);
    }

    // Deletar um autor
    public function destroy(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do autor não fornecido'], 400);
        }

        $author = Author::find($id);

        if (!$author) {
            return response()->json(['error' => 'Autor não encontrado'], 404);
        }

        $author->delete();
        return response()->json(['message' => 'Autor deletado com sucesso']);
    }

    // Restaurar um autor deletado
    public function restore(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do autor não fornecido'], 400);
        }

        $author = Author::withTrashed()->find($id);

        if (!$author) {
            return response()->json(['error' => 'Autor não encontrado'], 404);
        }

        $author->restore();
        return response()->json(['message' => 'Autor restaurado com sucesso']);
    }
}
