<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tasks;
use Spatie\Activitylog\Models\Activity;

class TasksController extends Controller
{
    /**
     * Get a listing of the tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tasks = Tasks::select();
        if ($assignee_id = $request->get('assignee_id')) {
            $tasks->where('assignee_id', '=', $assignee_id);
        }

        $tasks = $tasks->get();
        return response()->json([
            'status' => true,
            'data'   => $tasks
        ], 200);
    }

    /**
     * Create a new task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'title'       => 'required|max:255',
            'deadline'    => 'nullable|date|date_format:Y-m-d|after:now',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'status'      => 'nullable|in:prepared,in_progress,in_test,done'
        ]);

        $validated_data['owner_id'] = auth()->user()->getKey();
        $task = new Tasks($validated_data);
        $task->save();

        return response()->json([
            'status' => true,
            'data'   => $task
        ], 201);
    }

    /**
     * Get the task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json([
                'status' => false,
                'error' => __('Task not found')
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $task
        ], 200);
    }

    /**
     * Update the task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $viewer = auth()->user();
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json([
                'status' => false,
                'error' => __('Task not found')
            ], 404);
        }

        $validated_data = $request->validate([
            'title'       => 'required|max:255',
            'deadline'    => 'nullable|date|date_format:Y-m-d|after:now',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'status'      => 'nullable|in:prepared,in_progress,in_test,done'
        ]);

        $task = Tasks::find($id);
        $task->update($validated_data);

        return response()->json([
            'status' => true,
            'data'   => $task
        ], 200);
    }

    /**
     * Destroy the task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json([
                'status' => false,
                'error' => __('Task not found')
            ], 404);
        }

        $task->delete();
        return response()->json([
            'status' => true,
        ], 200);
    }

    public function activityLog()
    {
        $activity = Activity::where('subject_type', Tasks::class)->get();
        return response()->json([
           'status' => true,
           'data'   =>  $activity
        ]);
    }
}
