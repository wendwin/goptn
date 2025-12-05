<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index()
    {
        return Campus::with('majors')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kampus' => 'required',
            'kota' => 'required'
        ]);

        return Campus::create($request->all());
    }

    public function show($id)
    {
        $campus = Campus::with('majors')->find($id);

        if (!$campus) return response()->json(['message' => 'Kampus tidak ditemukan'], 404);

        return $campus;
    }

    public function update(Request $request, $id)
    {
        $campus = Campus::find($id);

        if (!$campus) return response()->json(['message' => 'Kampus tidak ditemukan'], 404);

        $campus->update($request->all());
        return response()->json(['message' => 'Data kampus diperbarui']);
    }

    public function destroy($id)
    {
        $campus = Campus::find($id);

        if (!$campus) return response()->json(['message' => 'Kampus tidak ditemukan'], 404);

        $campus->delete();
        return response()->json(['message' => 'Kampus dihapus']);
    }
}
