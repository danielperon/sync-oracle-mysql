<?php

namespace App\Services;

use App\Models\MySqlDB;
use App\Models\OracleDB;
use App\Models\OracleMigrationLog;
use Illuminate\Support\Facades\DB;
use App\Jobs\TransferJob;

class DataTransferService
{
    public function transferData($OracleTableName, $mysqlTableName)
    {
        $primaryKey = $this->getPrimaryKey($mysqlTableName);

        $lastProcessedId = DB::table($mysqlTableName)->max($primaryKey) ?? 0;

        $OracleDB = new OracleDB();
        $OracleDB->setTableName($OracleTableName);

        $OracleDB->where($primaryKey, '>', $lastProcessedId)
            ->orderBy($primaryKey)
            ->chunk(100, function ($rows) use ($mysqlTableName, $OracleDB) {
                $migrationStart = now();
                $batch = $rows->map(function ($row) {
                    $attributes = $row->getAttributes();
                    unset($attributes['rn']);
                    return $attributes;
                })->toArray();


                $logId = OracleMigrationLog::create([
                    'migration_start' => $migrationStart,
                    'migration_table' => $mysqlTableName,
                    'migration_type' => '1', //1 insert; 2 update
                    'migrated_records' => count($batch),
                    'completed' => false,
                ])->id;

                TransferJob::dispatch($mysqlTableName, $batch, $logId);
            });
    }

    /**
     * Get the primary key of a MySQL table.
     *
     * @param string $tableName
     * @return string
     */
    private function getPrimaryKey(string $tableName): string
    {
        $result = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'");

        return $result[0]->Column_name ?? 'id'; // Fallback to default primary key
    }
}
