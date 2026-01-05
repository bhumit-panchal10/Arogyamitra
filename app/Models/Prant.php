<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prant extends Model
{
    use HasFactory;

    protected $table = 'prant';

    protected $fillable = ['name', 'status'];

    public function vibhag()
    {
        return $this->hasOne(Vibhag::class, 'prant_id');
    }
}
