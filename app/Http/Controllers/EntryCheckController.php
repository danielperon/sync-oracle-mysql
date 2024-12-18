<?php

namespace App\Http\Controllers;

use App\Services\EntryTransferService;

class EntryCheckController extends Controller
{
    protected $entryTransferService;

    public function __construct(EntryTransferService $entryTransferService)
    {
        $this->entryTransferService = $entryTransferService;
    }

    public function checkEntries()
    {
        $this->entryTransferService->checkEntries();
        return response()->json(['status' => 'Entries processed successfully']);
    }
}
