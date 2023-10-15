<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = QueryBuilder::for(Task::class)
        ->allowedFilters('is_done')
        ->defaultSort('-created_at')
        ->allowedSorts(['title' , 'is_done' , 'created_at'])
        ->where('user_id' , Auth::user()->id)
        ->paginate();
        return new TaskResource($tasks);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;
        $task = Task::create($validated);
        return new TaskResource($task);
    }

    public function update(UpdateRequest $request , Task $task)
    {
        if(Auth::user()->id === $task->user_id){
            $validated = $request->validated();
            $task->update($validated);
            return new TaskResource($task);
        }
        return response()->noContent();

    }

    public function show(Task $task)
    {
        if(Auth::user()->id === $task->user_id){
            return new TaskResource($task);
        }
        return response()->noContent();
    }

    public function destroy(Task $task)
    {
        if(Auth::user()->id === $task->user_id){
            $task->delete();
            return response()->noContent();
        }
        return response()->json([
            "message" => "data not found"
        ]);
    }
}
