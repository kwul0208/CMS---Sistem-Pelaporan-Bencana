<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index() {
        $title = 'Position';
        return view('pages.position.index', compact('title'));
    }

    public function data() {
        return DataTables::of(Position::query())
            ->addColumn('parent_name', function (Position $position) {
                return $position->parent ? $position->parent->name : '-';
            })
            ->make(true);
    }


    public function create() {
        $positions = Position::all();
        return view('pages.position.form')->with('positions', $positions);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $validated['parent_id'] = $request->parent_id;

        Position::create($validated);
        return redirect()->route('position.index')->with('success', 'Position berhasil diupdate.');
    }

    public function edit(Position $position) {
        $model = $position;
        $positions = Position::all();
        return view('pages.position.form', compact('model', 'positions'));
    }

    public function update(Request $request, Position $position) {
        $data = $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
        ]);
        
        $position->update($data);

        return redirect()->route('position.index')->with('success', 'Position berhasil diupdate.');
    }

    public function destroy(Position $position) {
        if ($position->children()->exists()) {
            return redirect()->route('position.index')->with('error', 'Position tidak dapat dihapus karena memiliki relasi.');
        }

        $position->delete();
        return redirect()->route('position.index')->with('success', 'Position berhasil dihapus.');
    }

    public function getStructureOrg(){
        $positions = Position::where('id', '<>', 18)->with('users')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil.',
            'data' => $positions
        ]);
    }
}
