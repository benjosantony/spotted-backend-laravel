<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AcademyConfig extends Model
{
    protected $table = "sp_academyconfig";
    public $timestamps = false;
    public function scopeGetBonusRateByUserId($query, $userId)
    {
        return $query->join('sp_user', 'sp_user.academyLevel', '=', 'sp_academyconfig.level')
            ->where('sp_user.id', $userId)
            ->select('sp_academyconfig.bonusRate as bonusRate')->pluck('bonusRate');
    }
}
