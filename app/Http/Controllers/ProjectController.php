<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if we're accessing projects via the client route
        $routeClient = $request->route('client');

        if ($routeClient) {
            // We're accessing /clients/{client}/projects endpoint
            $client = Client::where('id', $routeClient)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$client) {
                return response()->json([
                    'message' => 'Client not found or does not belong to you'
                ], 404);
            }

            $projects = $client->projects()->latest()->get();
        } elseif ($request->has('client_id')) {
            // Filter by client_id query parameter
            $client = Client::where('id', $request->client_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$client) {
                return response()->json([
                    'message' => 'Client not found or does not belong to you'
                ], 404);
            }

            $projects = $client->projects()->latest()->get();
        } else {
            // Get projects from all clients of this user
            $clientIds = $request->user()->clients()->pluck('id');
            $projects = Project::whereIn('client_id', $clientIds)
                ->with('client')
                ->latest()
                ->get();
        }

        return response()->json([
            'projects' => $projects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,completed,on-hold,cancelled',
            'deadline' => 'nullable|date',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify the client belongs to the authenticated user
        $client = $request->user()->clients()->find($request->client_id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found or does not belong to you'
            ], 404);
        }

        $project = $client->projects()->create($request->all());

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $clientIds = $request->user()->clients()->pluck('id');
        $project = Project::whereIn('client_id', $clientIds)
            ->with('client')
            ->find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }

        return response()->json([
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,completed,on-hold,cancelled',
            'deadline' => 'nullable|date',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $clientIds = $request->user()->clients()->pluck('id');
        $project = Project::whereIn('client_id', $clientIds)->find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }

        // If client_id is being changed, verify it belongs to the user
        if ($request->has('client_id') && $request->client_id != $project->client_id) {
            $client = $request->user()->clients()->find($request->client_id);

            if (!$client) {
                return response()->json([
                    'message' => 'Client not found or does not belong to you'
                ], 404);
            }
        }

        $project->update($request->all());

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $clientIds = $request->user()->clients()->pluck('id');
        $project = Project::whereIn('client_id', $clientIds)->find($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }
}
