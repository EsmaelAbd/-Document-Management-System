<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('taggable_type')) {
            $taggableType = $request->input('taggable_type');
            $query->where('taggable_type', $taggableType);
        }

        $tags = $query->get();
        return response()->json($tags);
    }


    public function store(StoreCommentRequest $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'taggable_id' => 'required|int',
            'taggable_type' => 'required|string'
        ]);

        $tag = new Tag($validatedData);
        $tag->save();

        $taggable = $validatedData['taggable_type']::find($validatedData['taggable_id']);

        if ($taggable && method_exists($taggable, 'tags')) {
            $taggable->tags()->save($tag);
            return response()->json($tag, 201);
        }

        return response()->json(['error' => 'Invalid taggable type or ID.'], 404);
    }

    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    public function update(StoreCommentRequest $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $tag->fill($validatedData);
        $tag->save();

        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully.']);
    }
}
