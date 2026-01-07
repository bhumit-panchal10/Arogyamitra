<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    use HasFactory;

    protected $table = 'medicine_request';

    protected $fillable = [
        'id',
        'medicine_id',
        'arogyamitra_id',
        'qty',
        'status',
        'created_at',
        'updated_at',
        'gram_id',
        'app_user_id',
        'app_user_name',
        'delivered_quantity',
    ];

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
