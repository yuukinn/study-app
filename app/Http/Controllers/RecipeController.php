<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Recipe;
use App\Models\Category;
use App\MOdels\Step;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RecipeController extends Controller
{
    public function home(){

        // get recipes
        $recipes = Recipe::select(
            'recipes.id', 
            'recipes.title', 
            'recipes.description', 
            'recipes.created_at',
            'recipes.image',
            'users.name'
            )
            ->join('users', 'users.id', '=', 'recipes.user_id')
            ->orderBy('recipes.created_at', 'desc')
            ->limit(3)
            ->get();

        // 人気順のレシピを取得
        $popular = Recipe::select(
            'recipes.id', 
            'recipes.title', 
            'recipes.description', 
            'recipes.created_at',
            'recipes.image',
            'users.name'
            )
            ->join('users', 'users.id', '=', 'recipes.user_id')
            ->orderBy('recipes.views', 'desc')
            ->limit(2)
            ->get();

        return View('home', compact('recipes', 'popular'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        //
        $filters = $request->all();

        $query = Recipe::query()->select(
            'recipes.id', 
            'recipes.title', 
            'recipes.description', 
            'recipes.created_at',
            'recipes.image',
            'users.name',
            DB::raw('AVG(reviews.rating) as rating')
            )
            ->join('users', 'users.id', '=', 'recipes.user_id')
            ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
            ->groupBy('recipes.id')
            ->orderBy('recipes.created_at', 'desc');

        if(!empty($filters)) {
            // カテゴリーで絞り込み
            if(!empty($filters['categories'])) {
                // カテゴリーで絞り込みを選択したカテゴリーIDが含まれているレシピを取得
                $query->whereIn('recipes.category_id' , $filters['categories']);
            }

            if(!empty($filters['rating'])) {
                $query->havingRaw('AVG(reviews.rating) >= ?', [$filters['rating']])
                ->orderBy('rating', 'desc');
            }

            // タイトルで絞り込み
            if(!empty($filters['title'])) {
                $query->where('recipes.title', 'like', '%'.$filters['title'].'%');
            }
        }

        $recipes = $query->get();
        $categories = Category::all();


        return View('recipes.index', compact('recipes', 'categories', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();

        return view('recipes.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $posts = $request->all();
        $uuid = Str::uuid()->toString();

        $image = $request->file('image');
        // s3に画像をアップロード
        $path = Storage::disk('s3')->putFile('recipe', $image, 'public');

        // // s3のURL取得
        $url = Storage::disk('s3')->url($path);

        // DBにはURLを保存
        Recipe::insert([
            'id' => $uuid,
            'title' => $posts['title'],
            'description' => $posts['description'],
            'category_id' => $posts['category'],
            'image' => $url,
            'user_id' => Auth::id(),
        ]);

        $steps = [];
        foreach($posts['steps'] as $key => $step){
            $steps[$key] = [
                'recipe_id' => $uuid,
                'step_number' => $key + 1,
                'description' => $step,
            ];
        }
        STEP::insert($steps);
        dd($steps);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $recipe = Recipe::with(['ingredients', 'steps', 'reviews.user', 'user'])
            ->where('recipes.id', $id)
            ->first();
        
        $recipe_recorde = Recipe::find($id);
        $recipe_recorde->increment('views');

        return view('recipes.show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
