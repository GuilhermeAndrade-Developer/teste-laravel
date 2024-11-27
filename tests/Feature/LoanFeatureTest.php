<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoanFeatureTest extends TestCase
{
    private function authenticate()
    {
        // Cria um usuário com um e-mail aleatório
        $user = User::factory()->create([
            'email' => 'user_' . uniqid() . '@example.com',
        ]);
        $token = JWTAuth::fromUser($user);
        return [$user, $token];
    }

    public function test_it_can_list_loans()
    {
        [$user, $token] = $this->authenticate();

        // Certifique-se de que existam empréstimos no banco para listar
        Loan::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/loans');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_it_can_create_a_loan()
    {
        [$user, $token] = $this->authenticate();

        $book = Book::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/loans', [
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'loan_date'  => now()->toDateString(),
            'due_date'   => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'book_id',
                'loan_date',
                'due_date',
                'return_date',
            ]);
    }

    public function test_prevents_duplicate_loans_for_the_same_book()
    {
        [$user, $token] = $this->authenticate();

        $book = Book::factory()->create();

        // Primeiro empréstimo - deve ser permitido
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/loans', [
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'loan_date'  => now()->toDateString(),
            'due_date'   => now()->addDays(7)->toDateString(),
        ])->assertStatus(201);

        // Segundo empréstimo - deve ser bloqueado
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/loans', [
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'loan_date'  => now()->toDateString(),
            'due_date'   => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Este livro já está emprestado.',
            ]);
    }

    public function test_it_allows_new_loan_after_return()
    {
        [$user, $token] = $this->authenticate();

        $book = Book::factory()->create();

        // Primeiro empréstimo - deve ser permitido
        $loan = Loan::create([
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'loan_date'  => now()->toDateString(),
            'due_date'   => now()->addDays(7)->toDateString(),
            'return_date' => null,
        ]);

        // Registrar a devolução
        $loan->update(['return_date' => now()]);

        // Atualizar o status do livro para indicar que não está mais emprestado
        $book->update(['is_loaned' => false]);

        // Segundo empréstimo - deve ser permitido após a devolução
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/loans', [
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'loan_date'  => now()->toDateString(),
            'due_date'   => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'book_id',
                'loan_date',
                'due_date',
                'return_date',
            ]);
    }
}
