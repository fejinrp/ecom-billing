<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expdetail extends Model
{
    use HasFactory;

    protected $table = 'expdetails';
    protected $primaryKey = 'exp_id';
    public $timestamps = false;

    protected $fillable = [
        'exp_id',
        'exp_date',
        'exp_name',
        'exp_amount',
        'sname',
        'mexp_id',
        'estatus'
    ];

    public function category()
    {
        return $this->belongsTo(Expname::class, 'exp_name', 'exp_id');
    }

    public function staff()
    {
        return $this->belongsTo(Auser::class, 'sname', 'user_id');
    }
}