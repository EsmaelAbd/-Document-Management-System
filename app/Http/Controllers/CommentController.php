<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Document;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::paginate();
        return response()->json($comments);
    }

    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    public function storeForDocument(StoreCommentRequest $request, $documentId)
    {
        $validatedData = $request->validate([
            'body' => 'required|string',
        ]);

        $document = Document::findOrFail($documentId);

        $comment = new Comment($validatedData);
        $document->comments()->save($comment);

        return response()->json($comment, 201);
    }

    public function update(StoreCommentRequest $request, Comment $comment)
    {
        $validated = $request->validated();

        $comment->update($validated);

        return response()->json($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.'], 200);
    }
}
