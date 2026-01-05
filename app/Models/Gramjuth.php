<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Gramjuth extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'gramjuth';
    protected $fillable = [
        'name',
        'taluka_id',
        'status'
    ];

    /* public function taluka(): HasOne
    {
        return $this->HasOne(Taluka::class, 'id', 'taluka_id');
    } */


    public function gram()
    {
        return $this->hasMany(Gram::class, 'gramjuth_id');
    }

    public function jilla()
    {
        return $this->belongsTo(Taluka::class, 'jilla_id');
    }

    public function gramjuth()
    {
        return $this->hasOne(Gram::class, 'id');
    }

    public function taluka()
    {
        return $this->belongsTo(Taluka::class, 'taluka_id');
    }
}
