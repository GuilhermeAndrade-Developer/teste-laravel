<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Author;

class BookUnitTest extends TestCase
{

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_book()
    {
        $author = Author::factory()->create();
        $book = Book::create([
            'title' => 'Test Book',
            'publication_year' => 2022,
        ]);

        $book->authors()->attach($author);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'publication_year' => 2022,
        ]);
    }
}
