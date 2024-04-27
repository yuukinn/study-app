<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Step;
use App\Models\Ingredient;
use App\Http\Requests\RecipeCreateRequest;
use App\Http\Requests\RecipeUpdateRequest;
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
    public function store(RecipeCreateRequest $request)
    {
        $posts = $request->all();

        $uuid = Str::uuid()->toString();

        $image = $request->file('image');
        // s3に画像をアップロード
        $path = Storage::disk('s3')->putFile('recipe', $image, 'public');

        // // s3のURL取得
        $url = Storage::disk('s3')->url($path);


        try {
            DB::beginTransaction();
            // DBにはURLを保存
            Recipe::insert([
                'id' => $uuid,
                'title' => $posts['title'],
                'description' => $posts['description'],
                'category_id' => $posts['category'],
                'image' => $url,
                'user_id' => Auth::id(),
            ]);

            $ingredients = [];
            foreach($posts['ingredients'] as $key => $ingredient){
                $ingredients[$key] = [
                    'recipe_id' => $uuid,
                    'name' => $ingredient['name'],
                    'quantity' => $ingredient['quantity']
                ];
            } 
            Ingredient::insert($ingredients);

            $steps = [];
            foreach($posts['steps'] as $key => $step){
                $steps[$key] = [
                    'recipe_id' => $uuid,
                    'step_number' => $key + 1,
                    'description' => $step,
                ];
            }
            STEP::insert($steps);

            // コミット
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            \Log::debug(print_r($th->getMessage(), true));
            throw $th;
        }
        flash()->success('レシピ投稿しました');
        return redirect()->route('recipe.show', ['id' => $uuid ]);
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

        // レシピの投稿者とログインユーザが同じかどうか
        $is_my_recipe = false;
        if(Auth::check() && (Auth::id() === $recipe['user_id'])) {
            $is_my_recipe = true;
        }
        $is_reviewed = false;
        if(Auth::check()){
            $is_reviewed = $recipe->reviews->contains('user_id', Auth::id());
        }

        return view('recipes.show', compact('recipe', 'is_my_recipe', 'is_reviewed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipe = Recipe::with(['ingredients', 'steps', 'reviews.user', 'user'])
            ->where('recipes.id', $id)
            ->first();
        
        if( !Auth::check() || (Auth::id() != $recipe['user_id'])){
            abort(403);
        }
        $categories = Category::all();

        return view('recipes.edit', compact('recipe', 'categories'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeUpdateRequest $request, string $id)
    {
        $posts = $request->all();

        // 更新用データ
        $update_array = [
            'title' => $posts['title'],
            'description' => $posts['description'],
            'category_id' => $posts['category_id']
        ];

        //画像の分岐
        if ($request->hasFile('image') ){
            $image = $request->file('image');
            //s3に画像をアップロード
            $path = Storage::disk('s3')->putFile('recipe', $image, 'public');
            // s3のURLを取得
            $url = Storage::disk('s3')->url($path);
            // DBにはURLを保存
            $update_array['image'] = $url;
        }

        try {
            DB::beginTransaction();
            Recipe::where('id', $id)->update($update_array);
            
            // 古い材料を削除
            Ingredient::where('recipe_id', $id)->delete();

            //古い手順を削除
            STEP::where('recipe_id', $id)->delete();

            $ingredients = [];
            foreach($posts['ingredients'] as $key => $ingredient ){
                $ingredients[$key] = [
                    'recipe_id' => $id,
                    'name' => $ingredient['name'],
                    'quantity' =>$ingredient['quantity']
                ];
            }
            
            Ingredient::insert($ingredients);

            $steps = [];
            foreach($posts['steps'] as $key => $step) {
                $steps[$key] = [
                    'recipe_id' => $id,
                    'step_number' => $key + 1,
                    'description' => $step,
                ];
            }

            STEP::insert($steps);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::debug(print_r($th->getMessage(), true));
            throw $th;
        }

        flash()->success('レシピを更新しました!');

        return redirect()->route('recipe.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Recipe::where('id', $id)->delete();
        flash()->warning('レシピを削除しました!');

        return redirect()->route('home');
    }
}
