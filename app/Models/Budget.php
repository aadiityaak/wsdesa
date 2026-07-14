<?php

namespace App\Models;

use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    /** @use HasFactory<BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'budget_category_id', 'tahun', 'anggaran', 'realisasi', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'anggaran' => 'decimal:2',
            'realisasi' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }
}
