<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'Rojo',
                'code' => '#FF0000',
                'description' => 'Color rojo vibrante.',
            ],
            [
                'name' => 'Azul',
                'code' => '#0000FF',
                'description' => 'Color azul profundo.',
            ],
            [
                'name' => 'Verde',
                'code' => '#00FF00',
                'description' => 'Color verde fresco.',
            ],
            [
                'name' => 'Negro',
                'code' => '#000000',
                'description' => 'Color negro elegante.',
            ],
            [
                'name' => 'Blanco',
                'code' => '#FFFFFF',
                'description' => 'Color blanco puro.',
            ],
            [
                'name' => 'Gris',
                'code' => '#808080',
                'description' => 'Color gris neutro.',
            ],
            [
                'name' => 'Plateado',
                'code' => '#C0C0C0',
                'description' => 'Color plateado metálico.',
            ],
            [
                'name' => 'Amarillo',
                'code' => '#FFFF00',
                'description' => 'Color amarillo brillante.',
            ],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
