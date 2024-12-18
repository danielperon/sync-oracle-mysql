<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MySqlDB extends Model
{
    protected $connection = 'mysql';

    // Dynamically set the table name
    public function setTableName($tableName)
    {
        $this->table = $tableName;
    }
}
