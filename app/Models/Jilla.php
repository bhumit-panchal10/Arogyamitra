<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Taluka;
use App\Models\Vibhag;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jilla extends Model
{
    use HasFactory;

    protected $table = 'jilla';

    protected $fillable = ['name', 'vibhag_id', 'status'];

    public function taluka(): HasMany
    {
        return $this->hasMany(Taluka::class, 'jilla_id', 'id');
    }

    public function vibhag(): HasOne
    {
        return $this->hasOne(Vibhag::class, 'id', 'vibhag_id');
    }

    public function jilla()
    {
        return $this->hasOne(Taluka::class, 'id');
    }
}
