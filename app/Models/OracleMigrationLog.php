<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OracleMigrationLog extends Model
{
    use HasFactory;

    protected $table = 'oracle_migration_log';

    protected $fillable = [
        'migration_start',
        'migration_end',
        'migration_table',
        'migration_type',
        'migrated_records',
        'completed',
    ];

    public $timestamps = false;
}
