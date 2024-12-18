<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EntryTransferService;

class TransferOracleToMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:oracle-to-mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer data from Oracle to MySQL';

    protected $entryTransferService;

    public function __construct(EntryTransferService $entryTransferService)
    {
        parent::__construct();
        $this->entryTransferService = $entryTransferService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->entryTransferService->checkEntries();
        $this->info('Data transferred successfully!');
        return Command::SUCCESS;
    }
}
