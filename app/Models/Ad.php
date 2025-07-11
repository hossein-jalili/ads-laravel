<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['title', 'description', 'operator'];

    const OPERATORS = ['mci', 'irancell', 'rightel'];

    public static function isValidOperator($operator)
    {

        return in_array(strtolower($operator), self::OPERATORS);
    }
}
