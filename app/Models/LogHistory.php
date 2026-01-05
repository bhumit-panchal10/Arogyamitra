<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogHistory extends Model
{
    use HasFactory;

    protected $table = 'log_history';

    protected $fillable = [
        'method',
        'request_para',
        'request_url',
        'ip_address',
        'user_agent',
        'user_id'
    ];
}
