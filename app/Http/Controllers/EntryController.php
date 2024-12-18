<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    protected $entryService;

    public function __construct()
    {
        //$this->entryService = $entryService;
    }

    public function index(): JsonResponse
    {

        // Fetch all clients from the clients table
        $clients = DB::table('PORTAL.ENTRY_STATUS')->get();

        // Return the clients as a JSON response
        return response()->json($clients);
    }
}
