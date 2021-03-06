<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * @package App
 * @property string first_name
 * @property string last_name
 * @property int salary
 * @property boolean is_active
 * @property mixed hired_date
 * @property mixed updated_at
 * @property mixed created_at
 */
class Employee extends Model
{
    public $fillable = [
        'first_name',
        'last_name',
        'salary',
        'is_active',
        'hired_date',
    ];

    public function position() {
        return $this->hasOne(Position::class);
    }
}
