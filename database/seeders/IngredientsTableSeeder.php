<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $recipes = Recipe::pluck("id")->toArray();
        $ingredient_names = ['Salt', 'Sugar', 'Flour', 'Eggs', 'Milk', 'Butter', 'oil', 
        'Vanilla extract', 'Baking powder', 'Cocoa powder'];

        foreach ($recipes as $recipe) {
            for($i = 0; $i < rand(2, 5); $i++) {
                Ingredient::create([
                    'recipe_id' => $recipe,
                    'name' => $ingredient_names[array_rand($ingredient_names)],
                    'quantity' => rand(1, 500) . 'g',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
