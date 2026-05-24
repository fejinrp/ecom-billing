<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $table = 'agent';
    protected $primaryKey = 'acode';
    public $timestamps = false;

    protected $fillable = [
        'acode',
        'aname',
        'aplace',
        'amobile',
        'adate',
        'astatus'
    ];

    public function payments()
    {
        return $this->hasMany(Agentpay::class, 'acode', 'acode');
    }
}