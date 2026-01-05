<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vibhag extends Model
{
    use HasFactory;

    protected $table = 'vibhag';

    protected $fillable = ['name', 'prant_id', 'status'];

    public function jilla()
    {
        return $this->hasMany(Jilla::class, 'vibhag_id');
    }

    public function prant()
    {
        return $this->belongsTo(Prant::class, 'prant_id', 'id');
    }

}
