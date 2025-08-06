<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JournalService;

class JournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->journalService->createJournalInvoice(
                $request->input('noinvoice')
            );

            return response()->json(['message' => 'Journal entry created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Other methods (index, show, update, destroy) can be implemented if needed

    public function storeReturSales(Request $request)
    {
        try {
            $this->journalService->createJournalReturSales(
                $request->input('noretur')
            );

            return response()->json(['message' => 'Journal entry for Return Sales created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getVJournal()
    {
        $vJournal = \App\Models\Journal::join('m_coa', 'journal.KodeCOA', '=', 'm_coa.KodeCOA')
            ->select(
                'journal.id',
                'journal.tanggal',
                'journal.Transaksi',
                'journal.KodeCOA',
                'm_coa.NamaCOA',
                'journal.Keterangan',
                'journal.Debet',
                'journal.Credit'
            )
            ->get();

        return response()->json($vJournal);
    }
}
