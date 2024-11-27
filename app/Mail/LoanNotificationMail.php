<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $book;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.loan_notification')
            ->subject('Livro Emprestado com Sucesso')
            ->with([
                'user' => $this->user,
                'book' => $this->book,
            ]);
    }
}
