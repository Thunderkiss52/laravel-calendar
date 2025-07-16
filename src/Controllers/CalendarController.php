<?php
namespace Thunderkiss52\LaravelCalendar\Controllers;


use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Thunderkiss52\LaravelCalendar\Calendar\Tasks;

class CalendarController extends Controller
{

    public function day(string $date, bool $countOnly = false)
    {

        $dateObj = Carbon::parse($date);
        $result = [];

        $items = [
            'tasks' => Tasks::class,
            ...config('calendar.items')
        ];
        foreach ($items as $key => $func) {
            $dayData = (new $func)(auth()->user(), $dateObj);
            if (!is_null($dayData)) {
                $result[$key] = is_countable($dayData) && $countOnly && !is_int($dayData) ? $dayData->count() : $dayData;
            }
        }
        return $result;
    }

    public function month(int $year, int $month)
    {
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $result = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $result[$date->toDateString()] = $this->day($date->toDateString(), true);
            $date->addDay();
        }
        return $result;
    }

    public function week(string $monday)
    {
        $monday_date = Carbon::parse($monday);
        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $result[$monday_date->toDateString()] = $this->day($monday_date->toDateString(), false);
            $monday_date->addDays(1);
        }
        return $result;
    }
}
