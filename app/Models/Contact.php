<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_id', 'lead_id', 'email', 'phone'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }
}
