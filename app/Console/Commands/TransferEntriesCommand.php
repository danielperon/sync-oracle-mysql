<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EntryTransferService;

class TransferEntriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer entries from Oracle to MySQL';

    protected $service;

    /**
     * Create a new command instance.
     *
     * @param EntryTransferService $service
     */
    public function __construct(EntryTransferService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting data transfer...');
        $this->service->transferEntries();
        $this->info('Data transfer complete.');
        return 0;
    }
}
