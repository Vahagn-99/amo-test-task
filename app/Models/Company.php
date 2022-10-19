<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name'];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'company_id', 'id');
    }
}
