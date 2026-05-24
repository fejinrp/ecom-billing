<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agentpay extends Model
{
    use HasFactory;

    protected $table = 'agentpay';
    protected $primaryKey = 'payid';
    public $timestamps = false;

    protected $fillable = [
        'payid',
        'acode',
        'pdate',
        'pamount'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'acode', 'acode');
    }
}