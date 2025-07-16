<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

return [
    'markers' => [
        "test123" => [
            "color" => "blue",
            "icon" => "Check"
        ]
    ],
    'items' => [
        //"test123" => Test123::class
        /*"jobrequest" => fn($user, Carbon $date) => JobRequestData::collect(JobRequest::where('date', $date)->get()),
            //->filter(fn(PracticeDate $item) => $user->can('view', $item) && Chain::isPracticeFirst($item))->values()
            //->map(fn(JobRequest $jobRequest) => [
            //    "id" => $jobRequest->id,
            //])
            
        'candidate_waited' => function($user, Carbon $date) {
            $requests = JobRequest::where('date', $date)->where('moderated', 1)->get();
            return $requests->count() > 0 ? Candidate::whereBelongsTo($requests)->where('approved', 1)->whereNull('worked')->count() : null;
        },
        'candidate_approved' => function($user, Carbon $date) {
            $requests = JobRequest::where('date', $date)->where('moderated', 1)->get();
            return $requests->count() > 0 ? Candidate::whereBelongsTo($requests)->where('approved', 1)->where('worked', 1)->count(): null;
        },
        'candidate_deny' => function($user, Carbon $date) {
            $requests = JobRequest::where('date', $date)->where('moderated', 1)->get();
            return $requests->count() > 0 ? Candidate::whereBelongsTo($requests)->where('approved', 1)->where('worked', 0)->count(): null;
        }
        "holidays" => fn($user, Carbon $date) => Holiday::where([
            'day' => $date->day,
            'month' => $date->month,
            'year' => $date->year
        ])->first()*/
    ],
];