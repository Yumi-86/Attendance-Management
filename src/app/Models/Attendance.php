<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $casts = [
        'work_date' => 'datetime',
    ];

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

    public function getYearAttribute() {
        return $this->work_date ? Carbon::parse($this->work_date)->format('Y年') : null;
    }

    public function getDateAttribute() {
        return $this->work_date ? Carbon::parse($this->work_date)->format('n月j日') : null;
    }

    public function getTotalWorkMinutesAttribute() {
        if(!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $start = Carbon::parse($this->clock_in);
        $end = Carbon::parse($this->clock_out);

        $workMinutes = $end->diffInMinutes($start);

        $breakMinutes = $this->breakTimes->sum(fn($b) => $b->duration_minutes);

        $netMinutes = max($workMinutes - $breakMinutes,0);

        $hours = floor($netMinutes / 60);
        $minutes = $netMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getTotalBreakMinutesAttribute() {
        $totalMinutes = $this->breakTimes->sum(fn ($b) => $b->duration_minutes);

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function scopeDailyAttendanceSearch($query, $work_date) {
        return $this->whereDate('work_date', $work_date);
    }
}
