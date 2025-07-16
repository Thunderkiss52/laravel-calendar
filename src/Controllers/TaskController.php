<?php

namespace Thunderkiss52\LaravelCalendar\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Thunderkiss52\LaravelCalendar\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //Вывести список всех задач сотрудников
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));
        } else {
            $date = Carbon::now();
        }
        if ($date->dayOfWeek == 0) {
            $date->subDays(6);
        } else {
            $date->subDays($date->dayOfWeek);

        }
        $date->hour(0);
        $date->minute(0);
        $date->second(0);
        // throw new \Exception($date->dayOfWeek);
        // $tasks->each(function (Task $task) use ($result) {

        // });

        return Inertia::render("Tasks", [
            'items' => Inertia::lazy(function () use ($date) {
                $tasks = Task::where(function ($query) use ($date) {
                    $query
                        ->where('date', $date)
                        ->orWhere('date', Carbon::parse($date)->addDays(1))
                        ->orWhere('date', Carbon::parse($date)->addDays(2))
                        ->orWhere('date', Carbon::parse($date)->addDays(3))
                        ->orWhere('date', Carbon::parse($date)->addDays(4))
                        ->orWhere('date', Carbon::parse($date)->addDays(5))
                        ->orWhere('date', Carbon::parse($date)->addDays(6))
                        ->orWhere('date', Carbon::parse($date)->addDays(7));
                    // ->where('created_by', $user->id)
                    // ->orWhere('user_id', $user->id);
                })
                    ->with([
                        'user',
                        'createdBy'
                    ])
                    ->orderBy('date')
                    ->get();

                $result = [];
                foreach ($tasks as $task) {
                    if (!array_key_exists($task->user->id, $result)) {
                        $result[$task->user->id] = [
                            "name" => $task->user->name,
                            "tasks" => []
                        ];
                    }
                    $result[$task->user->id]['tasks'][] = $task;
                }
                return $result;
            }),
            "date" => $date->toISOString(),
            "label" => "Задачи сотрудников"
        ]);
    }

    public function byUser(User $user)
    {
        $tasks = Task::
            where(function ($query) use ($user) {
                $query
                    ->where('created_by', $user->id)
                    ->orWhere('user_id', $user->id);
            })->
            with([
                'user',
                'createdBy'
            ])
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($tasks as $task) {
            $d = $task->date->format('d.m.Y');
            if (!array_key_exists($d, $result)) {
                $result[$d] = [
                    "name" => $d,
                    "tasks" => []
                ];
            }
            $result[$d]['tasks'][] = $task;
        }

        return Inertia::render("Tasks", [
            'items' => $result,
            "label" => "Задачи сотрудника {$user->fullName()}"
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    // public function create(Task $task)
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],
            'name' => ['required', 'string', 'max:255']
        ]);

        return Task::create([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
        ]);
    }

    public function markFinished(Task $task)
    {
        abort_unless($task->user_id == Auth::id(), 400);
        $task->markFinished();
        return response(null, 200);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Task $task)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Task $task)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        abort_unless(!$task->finished_at, 400, 'Задача уже завершена');
        $task->deleteOrFail();
    }
}
