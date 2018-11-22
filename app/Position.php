<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Position
 * @package App
 * @property string title
 * @property mixed created_at
 * @property mixed updated_at
 */
class Position extends Model
{
    public $fillable = [
        'employee_id',
        'title',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
