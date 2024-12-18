<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataTransferService;

class TransferDataCommand extends Command
{
    protected $signature = 'transfer:data
                            {oracleTable : The Oracle table name}
                            {mysqlTable : The MySQL table name}';

    protected $description = 'Transfer data from Oracle to MySQL';

    protected $transferService;

    public function __construct(DataTransferService $transferService)
    {
        parent::__construct();
        $this->transferService = $transferService;
    }

    public function handle()
    {
        $oracleTable = $this->argument('oracleTable');
        $mysqlTable = $this->argument('mysqlTable');

        $this->info("Starting data transfer from {$oracleTable} to {$mysqlTable}...");

        $this->transferService->transferData($oracleTable, $mysqlTable);

        $this->info("Data transfer completed for {$oracleTable}.");
    }
}

