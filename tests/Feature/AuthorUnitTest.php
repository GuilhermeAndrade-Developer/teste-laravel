<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorUnitTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_it_can_create_an_author()
    {
        $author = Author::create([
            'name' => 'Test Author',
            'birth_date' => '1990-04-25',
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'Test Author',
            'birth_date' => '1990-04-25',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_it_can_soft_delete_an_author()
    {
        $author = Author::create([
            'name' => 'Test Author for Deletion',
            'birth_date' => '1985-06-15',
        ]);

        $author->delete();

        $this->assertSoftDeleted('authors', [
            'id' => $author->id,
        ]);
    }
}
