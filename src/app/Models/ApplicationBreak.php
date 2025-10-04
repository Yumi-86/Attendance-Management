<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApplicationBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'applied_break_start', 'applied_break_end',
    ];

    public function application() {
        return $this->belongsTo(Application::class);
    }
    public function getAppliedBreakStartFormattedAttribute() {
        return $this->applied_break_start
            ? Carbon::parse($this->applied_break_start)->format('H:i')
            : null ;
    }
    public function getAppliedBreakEndFormattedAttribute()
    {
        return $this->applied_break_end
            ? Carbon::parse($this->applied_break_end)->format('H:i')
            : null;
    }
}
