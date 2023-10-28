<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoodleTaskResource;
use App\Models\MoodleTask;
use Illuminate\Http\Request;

class MoodleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $url = $request->user()->moodle_url;
        abort_if($url === null, 400);

        $tasks = $request->user()->tasks()
            ->whereNull('completed_at')
            ->orWhere('completed_at', '>', now()->subWeek())
            ->get();

        return MoodleTaskResource::collection($tasks);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MoodleTask $task, Request $request)
    {
        $data = $request->validate(['completed' => 'required|boolean']);
        abort_unless($task->user_id === $request->user()->id, 401);

        $task->completed_at = $data['completed'] ? now() : null;
        $task->save();

        return $this->index($request);
    }

    /**
     * Mark tasks as completed in bulk by their Moodle ID
     * Migrates from old, local-only storage system in >1.1.0
     */
    public function migrate(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'numeric'
        ]);

        $moodleIds = collect($data['ids'])->map(fn($id) => "$id@moodle.macalester.edu");

        $request
            ->user()->tasks()
            ->whereIn('remote_id', $moodleIds)
            ->update(['completed_at' => now()]);
    }
}
