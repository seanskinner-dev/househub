<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['first_name', 'last_name', 'house_id', 'year_level', 'house_points'])]
class Student extends Model
{
    use HasFactory;

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Get the student's full name.
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
