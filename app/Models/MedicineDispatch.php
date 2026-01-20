<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDispatch extends Model
{
    protected $table = 'medicine_dispatch';
    use HasFactory;

    protected $fillable = [
        'id',
        'Stockiest_id',
        'medicine_id',
        'qty',
        'Entery_By',
        'created_at',
        'updated_at'

    ];

    public function medicine()
    {
        return $this->belongsTo(MedicineRequest::class, 'medicine_id');
    }

    public function medicineStock()
    {
        return $this->hasMany(Medicine::class, 'id');
    }
}
