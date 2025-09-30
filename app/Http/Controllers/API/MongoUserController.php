<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MongoUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        return response()->json([
            'data' => MongoUser::all(),
            'message' => 'Users retrieved successfully'
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mongodb.users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = MongoUser::create($request->all());
        
        return response()->json([
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(MongoUser $user)
    {
        return response()->json([
            'data' => $user,
            'message' => 'User retrieved successfully'
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, MongoUser $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:mongodb.users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());
        
        return response()->json([
            'data' => $user,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(MongoUser $user)
    {
        $user->delete();
        
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
