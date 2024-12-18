<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\OracleMigrationLog;


class TransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mysqlTableName;
    public $batch;
    public $logId;

    public function __construct($mysqlTableName, $batch, $logId)
    {

        $this->mysqlTableName = $mysqlTableName;
        $this->batch = $batch;
        $this->logId = (int) $logId;

    }

    public function handle()
    {
        $migrationEnd = now();

        try {
            DB::transaction(function () {
                DB::table($this->mysqlTableName)->insert($this->batch);
            });

            OracleMigrationLog::find($this->logId)->update([
                'migration_end' => $migrationEnd,
                'completed' => true,
            ]);

        } catch (\Exception $e) {
            OracleMigrationLog::where('id', $this->logId)
                ->update([
                    'migration_end' => $migrationEnd,
                    'completed' => false,
                ]);

            throw $e;
        }
    }

}
