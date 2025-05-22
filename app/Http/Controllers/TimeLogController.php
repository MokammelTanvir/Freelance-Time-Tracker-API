<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class TimeLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TimeLog::query()->with('project.client');

        // Filter by project_id if provided
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by date range if provided
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('start_time', [$request->from_date, $request->to_date]);
        } elseif ($request->has('from_date')) {
            $query->where('start_time', '>=', $request->from_date);
        } elseif ($request->has('to_date')) {
            $query->where('start_time', '<=', $request->to_date);
        }

        // Filter by is_billable
        if ($request->has('is_billable')) {
            $query->where('is_billable', $request->is_billable === 'true' ? 1 : 0);
        }

        // Filter by tags
        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tag) {
                $query->where('tags', 'LIKE', '%' . $tag . '%');
            }
        }

        // Make sure we only return time logs for projects that belong to the user
        $query->whereHas('project', function (Builder $query) use ($request) {
            $query->whereHas('client', function (Builder $query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        });

        // Sort by start_time desc
        $timeLogs = $query->latest('start_time')->get();

        return response()->json([
            'time_logs' => $timeLogs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'description' => 'nullable|string',
            'is_billable' => 'nullable|boolean',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify the project belongs to the authenticated user
        $project = Project::whereHas('client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->find($request->project_id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found or does not belong to you'
            ], 404);
        }

        $timeLog = TimeLog::create($request->all());

        return response()->json([
            'message' => 'Time log created successfully',
            'time_log' => $timeLog,
        ], 201);
    }

    /**
     * Start timing a new time log entry.
     */
    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'is_billable' => 'nullable|boolean',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify the project belongs to the authenticated user
        $project = Project::whereHas('client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->find($request->project_id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found or does not belong to you'
            ], 404);
        }

        // Check if there's already a running time log
        $runningTimeLog = TimeLog::whereNull('end_time')->first();
        if ($runningTimeLog) {
            return response()->json([
                'message' => 'There is already a running time log',
                'time_log' => $runningTimeLog,
            ], 400);
        }

        $timeLog = TimeLog::create([
            'project_id' => $request->project_id,
            'start_time' => Carbon::now(),
            'description' => $request->description,
            'is_billable' => $request->is_billable ?? true,
            'tags' => $request->tags,
            'hours' => 0,
        ]);

        return response()->json([
            'message' => 'Time tracking started',
            'time_log' => $timeLog,
        ], 201);
    }

    /**
     * Stop an active time log.
     */
    public function stop(Request $request, string $id): JsonResponse
    {
        // Find the time log and verify it belongs to the user
        $timeLog = TimeLog::whereHas('project', function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        })->find($id);

        if (!$timeLog) {
            return response()->json([
                'message' => 'Time log not found or does not belong to you'
            ], 404);
        }

        if ($timeLog->end_time) {
            return response()->json([
                'message' => 'Time log is already completed'
            ], 400);
        }

        $timeLog->end_time = Carbon::now();
        $timeLog->save();

        return response()->json([
            'message' => 'Time tracking stopped',
            'time_log' => $timeLog,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $timeLog = TimeLog::whereHas('project', function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        })->with('project.client')->find($id);

        if (!$timeLog) {
            return response()->json([
                'message' => 'Time log not found or does not belong to you'
            ], 404);
        }

        return response()->json([
            'time_log' => $timeLog,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|exists:projects,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'description' => 'nullable|string',
            'is_billable' => 'nullable|boolean',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the time log and verify it belongs to the user
        $timeLog = TimeLog::whereHas('project', function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        })->find($id);

        if (!$timeLog) {
            return response()->json([
                'message' => 'Time log not found or does not belong to you'
            ], 404);
        }

        // If changing project_id, verify the new project belongs to the user
        if ($request->has('project_id') && $request->project_id != $timeLog->project_id) {
            $project = Project::whereHas('client', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })->find($request->project_id);

            if (!$project) {
                return response()->json([
                    'message' => 'Project not found or does not belong to you'
                ], 404);
            }
        }

        $timeLog->update($request->all());

        return response()->json([
            'message' => 'Time log updated successfully',
            'time_log' => $timeLog,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        // Find the time log and verify it belongs to the user
        $timeLog = TimeLog::whereHas('project', function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        })->find($id);

        if (!$timeLog) {
            return response()->json([
                'message' => 'Time log not found or does not belong to you'
            ], 404);
        }

        $timeLog->delete();

        return response()->json([
            'message' => 'Time log deleted successfully',
        ]);
    }

    /**
     * Generate a summary report of time logs.
     */
    public function report(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = TimeLog::query()
            ->whereNotNull('end_time')
            ->whereBetween('start_time', [$request->from_date, $request->to_date])
            ->whereHas('project', function ($query) use ($request) {
                $query->whereHas('client', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);

                    if ($request->has('client_id')) {
                        $query->where('id', $request->client_id);
                    }
                });

                if ($request->has('project_id')) {
                    $query->where('id', $request->project_id);
                }
            })
            ->with('project.client');

        $timeLogs = $query->get();

        // Calculate totals
        $totalHours = $timeLogs->sum('hours');
        $billableHours = $timeLogs->where('is_billable', true)->sum('hours');
        $nonBillableHours = $timeLogs->where('is_billable', false)->sum('hours');

        // Group by project
        $projectSummary = $timeLogs->groupBy('project_id')->map(function ($logs, $projectId) {
            $project = $logs->first()->project;
            return [
                'project_id' => $projectId,
                'project_title' => $project->title,
                'client_name' => $project->client->name,
                'total_hours' => $logs->sum('hours'),
                'billable_hours' => $logs->where('is_billable', true)->sum('hours'),
                'billable_amount' => $logs->where('is_billable', true)->sum('hours') * $project->hourly_rate,
            ];
        })->values();

        // Group by day
        $dailySummary = $timeLogs->groupBy(function ($log) {
            return Carbon::parse($log->start_time)->format('Y-m-d');
        })->map(function ($logs, $date) {
            return [
                'date' => $date,
                'total_hours' => $logs->sum('hours'),
                'billable_hours' => $logs->where('is_billable', true)->sum('hours'),
            ];
        })->values();

        // Group by client
        $clientSummary = $timeLogs->groupBy('project.client.id')->map(function ($logs, $clientId) {
            $client = $logs->first()->project->client;
            return [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'total_hours' => $logs->sum('hours'),
                'billable_hours' => $logs->where('is_billable', true)->sum('hours'),
            ];
        })->values();

        return response()->json([
            'summary' => [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'total_hours' => $totalHours,
                'billable_hours' => $billableHours,
                'non_billable_hours' => $nonBillableHours,
            ],
            'by_project' => $projectSummary,
            'by_client' => $clientSummary,
            'by_day' => $dailySummary,
            'time_logs' => $timeLogs,
        ]);
    }
}
