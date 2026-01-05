<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medicine';
    use HasFactory;

    protected $fillable = [
        'name',
        'qty',
        'qty_type',
        'status'
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
