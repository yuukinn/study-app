<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $id)
    {
        Favorite::insert([
            'user_id' => Auth::id(),
            'recipe_id' => $id,
            'favorite' => 1,
        ]);

        $is_favorite = 1;

        return response()->json($is_favorite);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(string $id)
    {
        $favorite = Favorite::where('recipe_id', $id)
                    ->where('user_id', Auth::id())
                    ->first();
        
        if($favorite->favorite){
            $favorite->favorite = 0;
        } else {
            $favorite->favorite = 1;
        }

        $favorite -> save();

        return response()->json($favorite->favorite);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
