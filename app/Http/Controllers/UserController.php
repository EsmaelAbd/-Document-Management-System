<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, $userId)
    {
        $user = User::findOrFail($userId);
        $documents = $user->documents;
        return response()->json([
            'user' => $user,
            'documents' => $documents
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, $userId)
    {
        $user = User::findOrFail($userId);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }
        $user->update($validatedData);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        return response()->json(null, 204);
    }
}
