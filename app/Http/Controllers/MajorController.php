<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        return Major::with('campus')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'nama_jurusan' => 'required'
        ]);

        return Major::create($request->all());
    }

    public function show($id)
    {
        $major = Major::with('campus')->find($id);

        if (!$major) return response()->json(['message' => 'Jurusan tidak ditemukan'], 404);

        return $major;
    }

    public function update(Request $request, $id)
    {
        $major = Major::find($id);

        if (!$major) return response()->json(['message' => 'Jurusan tidak ditemukan'], 404);

        $major->update($request->all());

        return response()->json(['message' => 'Jurusan diperbarui']);
    }

    public function destroy($id)
    {
        $major = Major::find($id);

        if (!$major) return response()->json(['message' => 'Jurusan tidak ditemukan'], 404);

        $major->delete();
        return response()->json(['message' => 'Jurusan dihapus']);
    }
}
