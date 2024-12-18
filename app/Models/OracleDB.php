<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleDB extends Model
{
    protected $connection = 'oracle';

    // Dynamically set the table name
    public function setTableName($tableName)
    {
        $this->table = $tableName;
    }
}
