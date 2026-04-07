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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Идентификаторы
            $table->string('g_number')->nullable(); // очень большое число как строка (20+ цифр)
            $table->bigInteger('income_id')->nullable(); // номер дохода/поступления
            $table->string('odid')->nullable(); // может быть строкой (в примере "0")
            $table->bigInteger('nm_id')->nullable(); // номер номенклатуры

            // Даты и время
            $table->dateTime('date')->nullable(); // дата с временем (YYYY-MM-DD HH:MM:SS)
            $table->date('last_change_date')->nullable(); // только дата
            $table->dateTime('cancel_dt')->nullable(); // дата отмены (может быть NULL)

            // Товарные идентификаторы
            $table->string('supplier_article')->nullable(); // UUID поставщика
            $table->string('tech_size')->nullable(); // технический размер (UUID)
            $table->bigInteger('barcode')->nullable(); // числовой штрих‑код
            $table->string('subject')->nullable(); // UUID категории товара
            $table->string('category')->nullable(); // UUID категории
            $table->string('brand')->nullable(); // UUID бренда

            // Финансовые данные
            $table->decimal('total_price', 12, 2)->nullable(); // общая цена с точностью до копеек
            $table->integer('discount_percent')->nullable(); // процент скидки (целое число)

            // Локация
            $table->string('warehouse_name', 255)->nullable(); // название склада
            $table->string('oblast', 255)->nullable(); // область/регион

            // Булевы значения
            $table->boolean('is_cancel')->default(false); // флаг отмены операции

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
