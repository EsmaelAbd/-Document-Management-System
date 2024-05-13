<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())->get();
        return DocumentResource::collection($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:2048|mimetypes:document/pdf, document/doc, document/docx, document/txt'
        ]);

        $file = $request->document;
        $originalName = $file->getClientOriginalName();
        if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }
        $fileName = Str::random(32);
        $mime_type = $file->getClientMimeType();
        $type = explode('/', $mime_type);

        $path = Storage::disk('public')->put('documents', $file, $fileName . '.' . $type[1]);
        $path = Storage::disk('public')->url($path);

        // if ($request->hasFile('document')) {
        //     $file = $request->file('document');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $filePath = $file->storeAs('uploads', $fileName, 'public');

        //     $document = Document::create([
        //         'user_id' => Auth::id(),
        //         'name' => $fileName,
        //         'path' => $filePath,
        //         'mime_type' => $file->getClientMimeType(),
        //         'size' => $file->getSize(),
        //     ]);
        // }

        // return new DocumentResource($document);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document, $documentId)
    {
        $this->authorizeAccess($document);
        return new DocumentResource($document);

        $document = Document::findOrFail($documentId);
        $owner = $document->user;
        return response()->json([
            'document' => $document,
            'owner' => $owner
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $this->authorizeAccess($document);

        $data = $request->validate([
            'title' => 'required|max:255',
        ]);

        $document->update($data);

        return new DocumentResource($document);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorizeAccess($document);

        Storage::delete('public/' . $document->path);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully.'], 200);
    }

    protected function authorizeAccess(Document $document)
    {
        if (Auth::id() !== $document->user_id) {
            abort(403, "This action is unauthorized.");
        }
    }
}
