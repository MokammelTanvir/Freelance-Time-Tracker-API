<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $clients = $request->user()->clients()->latest()->get();

        return response()->json([
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $client = $request->user()->clients()->create($request->all());

        return response()->json([
            'message' => 'Client created successfully',
            'client' => $client,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $client = $request->user()->clients()->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }

        return response()->json([
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $client = $request->user()->clients()->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }

        $client->update($request->all());

        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $client = $request->user()->clients()->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }
}
