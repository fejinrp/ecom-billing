<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usercheck extends Model
{
    use HasFactory;

    protected $table = 'usercheck';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uid',
        'cat',
        'scat',
        'brand',
        'prod',
        'mprod',
        'purc',
        'mpurc',
        'astock',
        'slist',
        'sprice',
        'cinv',
        'minv',
        'linvc',
        'quot',
        'mquot',
        'estm',
        'mestm',
        'ord',
        'sord',
        'dord',
        'cord',
        'expen',
        'expd',
        'agent',
        'apay',
        'areport',
        'breport',
        'sreport',
        'preport',
        'stockr',
        'phistory',
        'excel',
        'auser',
        'usett',
        'csett',
        'backup',
        'restore'
    ];
}