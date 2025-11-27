<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reason;
use Yajra\DataTables\Facades\DataTables;

class ReasonController extends Controller
{
    public function index()
    {
        $reasons = Reason::all();
        if (request()->ajax()) {
            return DataTables()->of($reasons)
                ->addColumn('edit', function ($reason) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="' . $reason->id . '"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function ($reason) {
                    return '<form action="' . route('admin.reasons.destroy', $reason) . '" method="POST" class="frmDelete">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }
        return view('admin.reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('admin.reasons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:reasons,name',
        ]);
        Reason::create($request->only(['name', 'active']));
        return response()->json(['message' => 'Motivo registrado correctamente'], 200);
    }

    public function edit(Reason $reason)
    {
        return view('admin.reasons.edit', compact('reason'));
    }

    public function update(Request $request, Reason $reason)
    {
        $request->validate([
            'name' => 'required|unique:reasons,name,' . $reason->id,
        ]);
        $reason->update($request->only(['name', 'active']));
        return response()->json(['message' => 'Motivo actualizado correctamente'], 200);
    }

    public function destroy(Reason $reason)
    {
        $reason->delete();
        return response()->json(['message' => 'Motivo eliminado correctamente'], 200);
    }
}
