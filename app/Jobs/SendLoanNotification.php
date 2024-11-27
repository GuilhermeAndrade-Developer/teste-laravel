<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLoanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $book;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send('emails.loan_notification', ['user' => $this->user, 'book' => $this->book], function ($message) {
            $message->to($this->user->email)
                ->subject('Livro Emprestado com Sucesso');
        });
    }
}
