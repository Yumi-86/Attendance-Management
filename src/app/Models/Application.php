<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'attendance_id', 'applied_clock_in', 'applied_clock_out','applied_remarks', 'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function attendance() {
        return $this->belongsTo(Attendance::class);
    }

    public function application_breaks() {
        return $this->hasMany(ApplicationBreak::class);
    }

    public function getAppliedClockInFormattedAttribute() {
        return $this->applied_clock_in
            ? Carbon::parse($this->applied_clock_in)->format('H:i')
            : null;
    }
    public function getAppliedClockOutFormattedAttribute() {
        return $this->applied_clock_out
            ? Carbon::parse($this->applied_clock_out)->format('H:i')
            : null;
    }
}
