<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:500',
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:2048|mimetypes:application/pdf, application/doc, application/docx, application/txt',
            'name' => 'required|string|max:255',
            'taggable_id' => 'required|int',
            'taggable_type' => 'required|string',
        ];
    }
}
