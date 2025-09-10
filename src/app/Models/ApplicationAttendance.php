<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'applied_clock_in', 'applied_clock_out',
    ];

    public function application() {
        return $this->belongsTo(Application::class);
    }
}
