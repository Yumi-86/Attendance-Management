<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id', 'break_start', 'break_end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function getBreakStartFormattedAttribute() {
        return $this->break_start ? Carbon::parse($this->break_start)->format('H:i') : null;
    }
    public function getBreakEndFormattedAttribute() {
        return $this->break_end ? Carbon::parse($this->break_end)->format('H:i') : null;
    }

    public function getDurationMinutesAttribute() {
        if (!$this->break_start || !$this->break_end) return 0;

        return Carbon::parse($this->break_end)->diffInMinutes(Carbon::parse($this->break_start));
    }
}
