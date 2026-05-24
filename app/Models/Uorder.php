<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uorder extends Model
{
    use HasFactory;

    protected $table = 'uorder';
    protected $primaryKey = 'orderid';
    public $timestamps = false;

    protected $fillable = [
        'orderid',
        'userid',
        'utype',
        'paymethod',
        'total',
        'gamount',
        'tship',
        'pamount',
        'bamount',
        'discount',
        'gsta',
        'ostatus',
        'morderid',
        'install',
        'gsttin',
        'mcoin',
        'bcoin',
        'tcoin',
        'pcoin',
        'username',
        'orderdate'
    ];

    public function items()
    {
        return $this->hasMany(UorderItem::class, 'orderid', 'orderid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }
}