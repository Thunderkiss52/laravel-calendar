<?php
namespace Thunderkiss52\LaravelCalendar\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\User;
// use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Thunderkiss52\EloquentAuthorable\AuthorableTrait;
use Thunderkiss52\LaravelApprove\Notifications\ApproveUserNotification;
class Task extends Model
{
    use AuthorableTrait;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "calendar_tasks";
    protected $casts = [
        'finished_at' => 'datetime',
        'date' => 'date'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (is_null($model->user_id)) {
                $model->user_id = Auth::id();
            }
            if (is_null($model->date)) {
                $model->date = Carbon::now();
            }
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function markFinished()
    {
        $this->update([
            'finished_at' => Carbon::now()
        ]);
    }
}