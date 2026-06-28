<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
    ];

    /**
     * Get the purchase orders associated with this supplier.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(POrder::class, 'supplier_id', 'id');
    }
}
