<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gramjuth;
class Gram extends Model
{
    protected $table = 'gram';
    use HasFactory;

    protected $fillable = [
        'name',
        'gramjuth_id',
        'status'
    ];

    public function medicineRequest()
    {
        return $this->belongsTo(MedicineRequest::class, 'arogyamitra_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function gram()
    {
        return $this->belongsTo(User::class, 'gram_id');
    }

    public function gramjuth()
    {
        return $this->belongsTo(Gramjuth::class, 'gramjuth_id');
    }
}
