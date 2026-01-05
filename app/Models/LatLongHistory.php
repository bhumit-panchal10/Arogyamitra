<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatLongHistory extends Model
{
    use HasFactory;

    protected $table        = 'lat_long_history';
    protected $primaryKey   = 'id';

    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';
}
