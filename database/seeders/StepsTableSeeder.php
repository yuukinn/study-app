<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Step;

class StepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       //
        $recipes = Recipe::pluck('id')->toArray();
        foreach($recipes as $recipe){
            $numberOfSteps = rand(3,6);

            for($i = 1; $i <= $numberOfSteps; $i++) {
                Step::create([
                    'recipe_id' => $recipe,
                    'step_number' => $i,
                    'description' => 'Step '. $i . ' description for reicpe ' . $recipe,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
