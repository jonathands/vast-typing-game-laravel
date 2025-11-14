<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TextPassageResource;
use App\Models\TextPassage;
use Illuminate\Http\Request;

class TextPassageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $passages = TextPassage::query()->paginate(10);

        return TextPassageResource::collection($passages);
    }

    /**
     * Get a random passage.
     */
    public function random()
    {
        $passage = TextPassage::query()->inRandomOrder()->first();

        if (!$passage) {
            return response()->json(['message' => 'No passages available'], 404);
        }

        return new TextPassageResource($passage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TextPassage $textPassage)
    {
        return new TextPassageResource($textPassage);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TextPassage $textPassage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TextPassage $textPassage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TextPassage $textPassage)
    {
        //
    }
}
