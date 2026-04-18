<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Antipasti',
                'slug' => 'antipasti',
                'description' => 'Portate di apertura per iniziare il pasto'
            ],
            [
                'name' => 'Primi Piatti',
                'slug' => 'primi-piatti',
                'description' => 'Pasta, risotti, zuppe e minestre'
            ],
            [
                'name' => 'Secondi piatti',
                'slug' => 'secondi-piatti',
                'description' => 'Carne, pesce e piatti vegetariani principali'
            ],
            [
                'name' => 'Dolci',
                'slug' => 'dolci',
                'description' => 'Torte, dessert e preparazioni dolci'
            ],
            [
                'name' => 'Cocktails',
                'slug' => 'cocktails',
                'description' => 'Bevande miscelate alcoliche e analcoliche'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description']
                ]
            );
        }
    }
}
