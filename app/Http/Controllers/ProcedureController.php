<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * DEPRECATED CONTROLLER
 *
 * This controller contains stored procedure calls that were designed for multi-tenant architecture.
 * Since the database has been converted to single-tenant, these stored procedures may no longer
 * be compatible or relevant.
 *
 * Recommendation: Use the standard CRUD controllers instead (InvoiceController, etc.)
 * or update the stored procedures to work with the new single-tenant schema.
 */
class ProcedureController extends Controller
{
    public function deprecated(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'This controller is deprecated. Use standard CRUD controllers instead.',
            'recommendation' => 'Use InvoiceController, PartPenerimaanController, etc. for database operations.',
        ], 410); // 410 Gone status code
    }
}
