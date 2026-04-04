<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'category',
        'description'
    ];

    /**
     * Get the student that owns the transaction.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}