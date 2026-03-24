<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Имя таблицы, связанной с моделью.
     */
    protected $table = 'orders';

    /**
     * Поля, которые можно массово заполнять.
     */
    protected $fillable = [
        'g_number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'total_price',
        'discount_percent',
        'warehouse_name',
        'oblast',
        'income_id',
        'odid',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_cancel',
        'cancel_dt',
    ];

    /**
     * Поля, которые должны быть преобразованы в типы данных.
     */
    protected $casts = [
        'date' => 'datetime',
        'last_change_date' => 'date',
        'cancel_dt' => 'datetime',
        'total_price' => 'decimal:2',
        'discount_percent' => 'integer',
        'is_cancel' => 'boolean',
        'income_id' => 'integer',
        'barcode' => 'integer',
        'nm_id' => 'integer',
    ];
}
