<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'attendance_id', 'applied_remarks', 'type', 'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function attendance() {
        return $this->belongsTo(Attendance::class);
    }

    public function application_attendance() {
        return $this->hasOne(ApplicationAttendance::class);
    }

    public function application_breaks() {
        return $this->hasMany(ApplicationBreak::class);
    }
}
