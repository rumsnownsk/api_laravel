<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    /**
     * Имя таблицы, связанной с моделью.
     */
    protected $table = 'stocks';

    /**
     * Поля, которые можно массово заполнять.
     */
    protected $fillable = [
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'is_supply',
        'is_realization',
        'quantity_full',
        'warehouse_name',
        'in_way_to_client',
        'in_way_from_client',
        'nm_id',
        'subject',
        'category',
        'brand',
        'sc_code',
        'price',
        'discount',
    ];

    /**
     * Поля, которые должны быть преобразованы в типы данных.
     */
    protected $casts = [
        'date' => 'date',
        'last_change_date' => 'date',
        'quantity' => 'integer',
        'quantity_full' => 'integer',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',
        'in_way_to_client' => 'integer',
        'in_way_from_client' => 'integer',
        'nm_id' => 'integer',
        'barcode' => 'integer',
        'sc_code' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'integer',
    ];
}
