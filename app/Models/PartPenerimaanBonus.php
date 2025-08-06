<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaanBonus extends Model
{
    use HasFactory;

    protected $table = 'part_penerimaan_bonus';
    protected $primaryKey = ['KodeDivisi', 'NoPenerimaanBonus'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaanBonus',
        'TglPenerimaan',
        'KodeSupplier',
        'NoFaktur',
        'Status',
    ];
}
