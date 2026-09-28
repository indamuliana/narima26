<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    /**
     * Display searchable audit trail log of system transactions.
     */
    public function index(Request $request): View
    {
        $query = Activity::with('causer')->latest('id');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        if ($logName = $request->input('log_name')) {
            if ($logName !== 'SEMUA') {
                $query->where('log_name', $logName);
            }
        }

        $logs = $query->paginate(25)->withQueryString();

        $availableLogNames = Activity::distinct()->pluck('log_name')->filter()->values();

        return view('admin.audit-trail.index', compact('logs', 'availableLogNames', 'search', 'logName'));
    }
}
