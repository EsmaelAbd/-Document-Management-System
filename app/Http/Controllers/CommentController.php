<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function storeForDocument(Request $request, $documentId)
    {
        $validatedData = $request->validate([
            'body' => 'required|string',
        ]);

        $document = Document::findOrFail($documentId);

        $comment = new Comment($validatedData);
        $document->comments()->save($comment);

        return response()->json($comment, 201);
    }
}
