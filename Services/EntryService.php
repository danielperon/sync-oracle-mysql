<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EntryService
{
    public function getEntries()
    {
        // Query the ENTRY table from the PORTAL database
        return DB::connection('portal')->table('ENTRY')->get();
    }
}
