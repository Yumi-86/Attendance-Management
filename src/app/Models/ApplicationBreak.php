<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'applied_break_start', 'applied_break_end',
    ];

    public function application() {
        return $this->belongsTo(Application::class);
    }
}
