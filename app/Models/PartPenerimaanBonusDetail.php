<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaanBonusDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.part_penerimaan_bonus_detail';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaanBonus',
        'KodeBarang',
        'QtySupply',
    ];
}
