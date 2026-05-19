<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'employee_id',
        'total_amount',
        'cash_paid',
        'change_returned',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
