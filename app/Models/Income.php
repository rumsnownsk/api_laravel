<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    /**
     * Поля, которые можно массово заполнять.
     */
    protected $fillable = [
        'income_id',
        'number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'total_price',
        'date_close',
        'warehouse_name',
        'nm_id',
    ];

    /**
     * Поля, которые должны быть преобразованы в типы данных.
     */
    protected $casts = [
        'date' => 'date',
        'last_change_date' => 'date',
        'date_close' => 'date',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'income_id' => 'integer',
        'barcode' => 'integer',
        'nm_id' => 'integer',
    ];
}
