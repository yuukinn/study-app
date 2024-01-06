<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Review;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $recipes = Recipe::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        $comments = [
            'Great recipe!',
            'I loved it!',
            'Will try agein.',
            'Not my favorite.',
            'Easy to make and delicious!',
        ];

        foreach($recipes as $recipe){
            for($i = 0; $i < rand(1, 3); $i++) {
                Review::create([
                    'user_id' => $users[array_rand($users)],
                    'recipe_id' => $recipe,
                    'rating' => rand(1, 5),
                    'comment' => $comments[array_rand($comments)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }
}
