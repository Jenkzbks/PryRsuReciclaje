<?php

namespace App\Http\Controllers;

use App\Models\SchedulingChange;
use App\Models\Scheduling;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SchedulingChangeController extends Controller
{
    /**
     * Muestra el historial de cambios.
     */
    public function index()
    {
        $changes = SchedulingChange::with(['scheduling', 'reason', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('scheduling_changes.index', compact('changes'));
    }

    /**
     * Endpoint para DataTables AJAX
     */
    public function data(Request $request)
    {
        $query = \App\Models\SchedulingChange::with(['reason', 'user', 'scheduling.group']);
        return DataTables::of($query)
            ->addColumn('reason', function($row) {
                return $row->reason?->name ?? '-';
            })
            ->addColumn('user', function($row) {
                return $row->user?->name ?? '-';
            })
            ->editColumn('change_type', function($row) {
                return ucfirst($row->change_type);
            })
            ->editColumn('notes', function($row) {
                return $row->notes ? $row->notes : 'Sin notas registradas';
            })
            ->editColumn('scheduling_id', function($row) {
                if ($row->scheduling && $row->scheduling->group && $row->scheduling->date) {
                    return $row->scheduling->group->name . ' - ' . date('d/m/Y', strtotime($row->scheduling->date));
                }
                return '-';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '';
            })
            ->make(true);
    }
}
