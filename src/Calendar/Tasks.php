<?php
namespace Thunderkiss52\LaravelCalendar\Calendar;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\Signdocument\Models\SignDocument;
use Thunderkiss52\LaravelCalendar\Models\Task;

class Tasks
{
    public function __invoke($user, Carbon $date): Collection
    {
        return Task::withTrashed()->whereDate('date', $date)
            ->where(function ($query) use ($user) {
                $query
                    ->where('created_by', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->with([
                'user',
                'createdBy'
            ])
            ->get();
    }
}
// ->values();
// ->map(fn(SignDocument $signDocument) => [
//     'id' => $signDocument->uuid,
//     'name' => $signDocument->name,
//     'type' => $signDocument->type::$label,
//     'user' => $signDocument->createdBy->name,
//     'status' => $signDocument->status,
// ]);
