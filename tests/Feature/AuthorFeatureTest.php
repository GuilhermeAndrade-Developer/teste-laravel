<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate()
    {
        $user = User::factory()->create();
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        return $token;
    }

    public function test_it_can_list_authors()
    {
        $token = $this->authenticate();

        Author::factory()->count(5)->create();

        $response = $this->getJson('/api/authors', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_it_can_create_an_author()
    {
        $token = $this->authenticate();

        $response = $this->postJson('/api/authors', [
            'name' => 'Author Name',
            'birth_date' => '1980-05-12',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'birth_date',
            ]);
    }

    public function test_it_can_update_an_author()
    {
        $token = $this->authenticate();

        $author = Author::factory()->create();

        $response = $this->putJson("/api/authors/update?id={$author->id}", [
            'name' => 'Updated Author Name',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Author Name',
            ]);
    }

    public function test_it_can_delete_an_author()
    {
        $token = $this->authenticate();

        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/delete?id={$author->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Autor deletado com sucesso',
            ]);
    }
}
