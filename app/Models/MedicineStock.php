<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineStock extends Model
{
    use HasFactory;

    protected $table        = 'medicine_stock';
    protected $primaryKey   = 'id';

    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';

    protected $fillable = ['arogyamitra_id', 'medicine_id', 'qty'];


    public function medicine()
    {
        return $this->belongsTo(MedicineStock::class, 'medicine_id');
    }

    public function medicineStock()
    {
        return $this->belongsTo(MedicineStock::class, 'medicine_id');
    }
}
