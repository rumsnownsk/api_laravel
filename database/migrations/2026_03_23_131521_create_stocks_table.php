<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Даты
            $table->date('date')->nullable(); // дата в формате YYYY-MM-DD
            $table->date('last_change_date')->nullable(); // дата последнего изменения

            // Идентификаторы
            $table->string('supplier_article')->nullable(); // UUID поставщика
            $table->string('tech_size')->nullable(); // технический размер (UUID)
            $table->bigInteger('barcode')->nullable(); // числовой штрих-код
            $table->bigInteger('nm_id')->nullable(); // номер номенклатуры
            $table->string('subject')->nullable(); // UUID категории товара
            $table->string('category')->nullable(); // UUID категории
            $table->string('brand')->nullable(); // UUID бренда
            $table->bigInteger('sc_code')->nullable(); // код склада/системы

            // Количественные показатели
            $table->integer('quantity')->nullable(); // текущее количество
            $table->integer('quantity_full')->nullable(); // полное количество

            // Финансовые данные
            $table->decimal('price', 10, 2)->nullable(); // цена с точностью до копеек
            $table->integer('discount')->nullable(); // процент скидки (целое число)

            // Булевы и статусы
            $table->boolean('is_supply')->nullable(); // флаг поставки
            $table->boolean('is_realization')->nullable(); // флаг реализации
            $table->integer('in_way_to_client')->nullable(); // в пути к клиенту
            $table->integer('in_way_from_client')->nullable(); // в пути от клиента

            // Локация
            $table->string('warehouse_name', 255)->nullable(); // название склада

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
