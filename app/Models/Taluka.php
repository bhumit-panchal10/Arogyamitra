<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Taluka extends Model
{
    use HasFactory;
    protected $table = 'taluka';

    protected $fillable = ['name', 'jilla_id', 'status'];

    public function gramjuth(): HasMany
    {
        return $this->hasMany(Gramjuth::class, 'taluka_id', 'id');
    }

    public function jilla(): HasOne
    {
        return $this->hasOne(Jilla::class, 'id', 'jilla_id');
    }

}
