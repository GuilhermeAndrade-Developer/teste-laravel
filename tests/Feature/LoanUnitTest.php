<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Book;

class LoanUnitTest extends TestCase
{

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_loan()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now()->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => $loan->loan_date,
            'return_date' => $loan->return_date,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_checks_if_book_is_already_loaned()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now(),
            'return_date' => now()->addDays(7),
        ]);

        $isLoaned = Loan::where('book_id', $book->id)->exists();

        $this->assertTrue($isLoaned);
    }
}
