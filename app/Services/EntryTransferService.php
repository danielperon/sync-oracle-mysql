<?php

namespace App\Services;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\OracleMigrationLog;
use App\Models\OracleEntry;
use App\Models\MySqlEntry;
use App\Jobs\EntryTransferJob;

class EntryTransferService
{
    protected $cacheKey = 'entries_last_checked';


    public function transferEntries()
    {
        $lastChecked = Cache::get($this->cacheKey, now()->startOfDay());

        $mysqlTableName = (new MySqlEntry())->getTable();

       /* $count = OracleEntry::where(function ($query) use ($lastChecked) {
            $query->where('entry_created', '>', $lastChecked)
                ->orWhere('entry_modified', '>', $lastChecked);
        })
            ->count();

        dd($count);

// Print the SQL query with bindings
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        dd(vsprintf(str_replace('?', "'%s'", $sql), $bindings));*/

        OracleEntry::where(function ($query) use ($lastChecked) {
            $query->where('entry_created', '>', $lastChecked)
                ->orWhere('entry_modified', '>', $lastChecked);
        })
            ->orderBy('entry_created')
            ->chunk(100, function ($entries) use($lastChecked, $mysqlTableName) {
                $migrationStart = now();

                $batch = $entries->map(function ($entry) {
                    $entryArray = $entry->toArray();
                    unset($entryArray["rn"]);
                    return $entryArray;
                });


                $insertBatch = [];
                $updateBatch = [];

                foreach ($batch as $entry) {

                    $entryArray = (array) $entry;


                    if ($entry["entry_created"] > $lastChecked) {
                        $insertBatch[] = $entryArray;
                    } elseif ($entry["entry_modified"] > $lastChecked) {
                        $updateBatch[] = $entryArray;
                    }
                }


                if (!empty($insertBatch)) {

                    $logId = OracleMigrationLog::create([
                        'migration_start' => $migrationStart,
                        'migration_table' => $mysqlTableName,
                        'migrated_records' => count($batch),
                        'completed' => false,
                        'migration_type' => 1,
                    ])->id;

                    EntryTransferJob::dispatch($mysqlTableName, $insertBatch, $logId, 1); // 1- insert

                    echo "Running Insert Batch: ".$insertBatch[0]["entry_id"]."\n";


                }

                if (!empty($updateBatch)) {

                    $logId = OracleMigrationLog::create([
                        'migration_start' => $migrationStart,
                        'migration_table' => $mysqlTableName,
                        'migrated_records' => count($batch),
                        'completed' => false,
                        'migration_type' => 2,
                    ])->id;

                    EntryTransferJob::dispatch($mysqlTableName, $updateBatch, $logId, 2); // 2- update

                    echo "Running Update Batch: ".$updateBatch[0]["entry_id"]."\n";

                }

                Cache::put($this->cacheKey, now());

            });
    }
}
