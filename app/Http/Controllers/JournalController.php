<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JournalService;
use App\Models\Journal;

class JournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Limit untuk testing Laravel - hanya 10 data terbaru
            $journals = Journal::with(['coa'])
                             ->orderBy('tanggal', 'desc')
                             ->limit(10)
                             ->get();
            $data = $journals->map(fn($j)=>[
                'id' => $j->id,
                'tanggal' => $j->tanggal,
                'transaksi' => $j->transaksi,
                'kodeCoa' => $j->kodecoa,
                'namaCoa' => optional($j->coa)->namacoa,
                'keterangan' => $j->keterangan,
                'debet' => (float)$j->debet,
                'kredit' => (float)$j->kredit
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Data journals retrieved successfully (limited to 10 for testing)',
                'data' => $data,
                'totalCount' => $data->count(),
                'note' => 'Data limited to 10 records for Laravel testing'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve journals data',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $vJournal = \App\Models\Journal::join('dbo.m_coa', 'dbo.journal.kodecoa', '=', 'dbo.m_coa.kodecoa')
            ->select(
                'dbo.journal.id',
                'dbo.journal.tanggal',
                'dbo.journal.transaksi',
                'dbo.journal.kodecoa',
                'dbo.m_coa.namacoa',
                'dbo.journal.keterangan',
                'dbo.journal.debet',
                'dbo.journal.kredit'
            )
            ->orderBy('dbo.journal.tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Journal data retrieved successfully',
            'data' => $vJournal
        ]);
    }

    /**
     * Get all journals for frontend (no limit)
     */
    public function getAllForFrontend()
    {
        try {
            $journals = Journal::with(['coa'])
                             ->orderBy('tanggal', 'desc')
                             ->get();
            $data = $journals->map(fn($j)=>[
                'id' => $j->id,
                'tanggal' => $j->tanggal,
                'transaksi' => $j->transaksi,
                'kodeCoa' => $j->kodecoa,
                'namaCoa' => optional($j->coa)->namacoa,
                'keterangan' => $j->keterangan,
                'debet' => (float)$j->debet,
                'kredit' => (float)$j->kredit
            ]);
            return response()->json([
                'success' => true,
                'message' => 'All journal data retrieved for frontend',
                'data' => $data,
                'totalCount' => $data->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all journal data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
