<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;
    protected $keyType = 'string';

    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
