<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Ciencia Ficción' => [
                ['title' => 'Dune', 'author' => 'Frank Herbert', 'published_year' => 1965, 'description' => 'Novela clásica ambientada en el planeta Arrakis.'],
                ['title' => 'Fundación', 'author' => 'Isaac Asimov', 'published_year' => 1951, 'description' => 'El comienzo del declive del Imperio Galáctico.'],
                ['title' => 'Neuromante', 'author' => 'William Gibson', 'published_year' => 1984, 'description' => 'Obra seminal del género ciberpunk.'],
            ],
            'Desarrollo de Software' => [
                ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'published_year' => 2008, 'description' => 'Manual de estilo y desarrollo ágil de software.'],
                ['title' => 'Design Patterns', 'author' => 'Erich Gamma et al.', 'published_year' => 1994, 'description' => 'Elementos de software orientado a objetos reutilizable.'],
                ['title' => 'Refactoring', 'author' => 'Martin Fowler', 'published_year' => 1999, 'description' => 'Mejorando el diseño de código existente.'],
            ],
            'Filosofía' => [
                ['title' => 'Meditaciones', 'author' => 'Marco Aurelio', 'published_year' => 180, 'description' => 'Reflexiones estoicas personales.'],
                ['title' => 'La República', 'author' => 'Platón', 'published_year' => 375, 'description' => 'Diálogo platónico sobre la justicia y la ciudad ideal.'],
            ],
            'Categoría Sin Libros' => [],
        ];

        foreach ($categories as $categoryName => $books) {
            $cat = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($books as $bookData) {
                Book::firstOrCreate(
                    [
                        'title' => $bookData['title'],
                        'category_id' => $cat->id,
                    ],
                    [
                        'author' => $bookData['author'],
                        'published_year' => $bookData['published_year'],
                        'description' => $bookData['description'],
                    ]
                );
            }
        }
    }
}
