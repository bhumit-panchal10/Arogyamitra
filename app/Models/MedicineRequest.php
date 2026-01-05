<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    use HasFactory;

    protected $table = 'medicine_request';

    public function medicine()
    {
        return $this->hasOne(Medicine::class, 'id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function gram()
    {
        return $this->hasOne('App\Models\User', 'gram_id');
    }
}
