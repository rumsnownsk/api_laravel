<?php

namespace App\Services;

class ImportModelFactory
{
    public static function create(string $type): \Illuminate\Database\Eloquent\Model
    {
        return match ($type) {
            'sales' => new \App\Models\Sale(),
            'orders' => new \App\Models\Order(),
            'stocks' => new \App\Models\Stock(),
            'incomes' => new \App\Models\Income(),
            default => throw new \InvalidArgumentException("Unknown import type: $type"),
        };
    }
}
