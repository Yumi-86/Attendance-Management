<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'work_date', 'clock_in', 'clock_out', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public static function todayRecord($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('work_date', today())
            ->first();
    }

    public function getClockInFormattedAttribute() {
        return $this->clock_in ? Carbon::parse($this->clock_in)->format('H:i') : null;
    }

    public function getClockOutFormattedAttribute() {
        return $this->clock_out ? Carbon::parse($this->clock_out)->format('H:i') : null;
    }
}
