<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceRecordController extends Controller
{
    public function edit(Maintenance $maintenance, MaintenanceRecord $record)
    {
        if ($record->schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        $estados = [
            'no realizado' => 'No realizado',
            'realizado'    => 'Realizado',
        ];

        return view('maintenancerecords.edit', compact('maintenance', 'record', 'estados'));
    }

    public function update(Request $request, Maintenance $maintenance, MaintenanceRecord $record)
    {
        if ($record->schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        $data = $request->validate([
            'descripcion' => 'nullable|string',
            'estado'      => 'required|in:no realizado,realizado',
            'image'       => 'nullable|image|max:4096',
        ]);

        $record->descripcion = $data['descripcion'] ?? '';
        $record->estado      = $data['estado'];

        if ($request->hasFile('image')) {

            if ($record->image_url) {
                Storage::disk('public')->delete($record->image_url);
            }

            $path = $request->file('image')->store('maintenance', 'public');
            $record->image_url = $path;
        }

        $record->save();

        return redirect()
            ->route('admin.maintenances.schedules.index', $maintenance)
            ->with('success', 'Registro de mantenimiento actualizado.');
    }

    public function destroy(Maintenance $maintenance, MaintenanceRecord $record)
    {
        if ($record->schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        if ($record->image_url) {
            Storage::disk('public')->delete($record->image_url);
        }

        $record->delete();

        return redirect()
            ->route('admin.maintenances.schedules.index', $maintenance)
            ->with('success', 'Registro de mantenimiento eliminado.');
    }
}
