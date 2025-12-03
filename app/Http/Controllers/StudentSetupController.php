<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\StudentPTNChoice;
use App\Models\StudentEntryPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSetupController extends Controller
{
    public function setup(Request $request)
    {
        $request->validate([
            'profile.school_name' => 'required|string',
            'profile.city' => 'required|string',
            'profile.average_grade' => 'required|numeric',

            'ptn_choices' => 'required|array|min:1',
            'ptn_choices.*.university_name' => 'required|string',
            'ptn_choices.*.major' => 'required|string',

            'entry_paths' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            StudentProfile::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'school_name' => $request->profile['school_name'],
                    'city' => $request->profile['city'],
                    'average_grade' => $request->profile['average_grade']
                ]
            );

            StudentPTNChoice::where('user_id', auth()->id())->delete();

            foreach ($request->ptn_choices as $choice) {
                StudentPTNChoice::create([
                    'user_id' => auth()->id(),
                    'university_name' => $choice['university_name'],
                    'major' => $choice['major']
                ]);
            }

            StudentEntryPath::where('user_id', auth()->id())->delete();

            foreach ($request->entry_paths as $path) {
                StudentEntryPath::create([
                    'user_id' => auth()->id(),
                    'path' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data siswa berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            if ($request->has('profile')) {
                StudentProfile::updateOrCreate(
                    ['user_id' => auth()->id()],
                    [
                        'school_name' => $request->profile['school_name'] ?? null,
                        'city' => $request->profile['city'] ?? null,
                        'average_grade' => $request->profile['average_grade'] ?? null
                    ]
                );
            }

            if ($request->has('ptn_choices')) {
                StudentPTNChoice::where('user_id', auth()->id())->delete();

                foreach ($request->ptn_choices as $choice) {
                    StudentPTNChoice::create([
                        'user_id' => auth()->id(),
                        'university_name' => $choice['university_name'],
                        'major' => $choice['major']
                    ]);
                }
            }

            if ($request->has('entry_paths')) {
                StudentEntryPath::where('user_id', auth()->id())->delete();

                foreach ($request->entry_paths as $path) {
                    StudentEntryPath::create([
                        'user_id' => auth()->id(),
                        'path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data berhasil diperbarui']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy()
    {
        DB::beginTransaction();

        try {
            $userId = auth()->id();

            StudentProfile::where('user_id', $userId)->delete();
            StudentPTNChoice::where('user_id', $userId)->delete();
            StudentEntryPath::where('user_id', $userId)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Semua data student berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
