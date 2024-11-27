<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookFeatureTest extends TestCase
{

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_list_books()
    {
        $token = JWTAuth::fromUser($this->user);

        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_book()
    {
        $token = JWTAuth::fromUser($this->user);

        $author = Author::factory()->create();

        $response = $this->postJson('/api/books', [
            'title' => 'New Book',
            'publication_year' => 2022,
            'authors' => [$author->id],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'title', 'publication_year', 'authors',
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_authors_when_creating_a_book()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->postJson('/api/books', [
            'title' => 'Invalid Book',
            'publication_year' => 2022,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['authors']);
    }
}
