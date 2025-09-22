<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scopeToday($query, $userId)
    {
        return $query->where('user_id', $userId)
                    ->whereDate('work_date', today());
    }
}
