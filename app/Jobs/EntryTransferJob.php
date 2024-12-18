<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\OracleMigrationLog;


class EntryTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mysqlTableName;
    protected $batch;
    protected $logId;
    protected $type;
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param string $mysqlTableName
     * @param array $batch
     * @param int $logId
     * @param string $type
     */
    public function __construct(string $mysqlTableName, array $batch, int $logId, int $type)
    {
        $this->mysqlTableName = $mysqlTableName;
        $this->batch = $batch;
        $this->logId = $logId;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $migrationEnd = now();

        try {
            DB::transaction(function () {
                foreach ($this->batch as $entry) {
                    if ($this->type === 1) {
                        DB::table($this->mysqlTableName)->insert($entry);
                    } elseif ($this->type === 2) {
                        DB::table($this->mysqlTableName)->updateOrInsert(
                            ['entry_id' => $entry['entry_id']], // Match by primary key
                            $entry
                        );
                    }
                }
            });

            OracleMigrationLog::find($this->logId)->update([
                'migration_end' => $migrationEnd,
                'migration_type' => $this->type,
                'completed' => true,
            ]);

            \Log::info("Entry Batch processed successfully", [
                'table' => $this->mysqlTableName,
                'type' => $this->type,
                'batch_size' => count($this->batch),
            ]);

        } catch (\Exception $e) {

            OracleMigrationLog::find($this->logId)->update([
                'migration_end' => $migrationEnd,
                'migration_type' => $this->type,
                'completed' => false,
            ]);

            \Log::error("Error processing Entry batch", [
                'table' => $this->mysqlTableName,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Job failed', [
            'error' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);
    }
}
