<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'publication_year' => $this->faker->year(),
        ];
    }

    /**
     * Configure the factory to create relationships.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Book $book) {
            // Cria entre 1 e 3 autores para cada livro
            $authors = Author::factory()->count(rand(1, 3))->create();
            $book->authors()->attach($authors);
        });
    }
}
