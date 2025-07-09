<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index() {
        return view('pages.users.index');
    }

    public function data() {
        return DataTables::of(User::query())
            ->addColumn('position_name', function (User $user) {
                return $user->position ? $user->position->name : '-';
            })
            ->make(true);
    }


    public function create() {
        $positions = Position::all();
        return view('pages.users.form')->with('positions', $positions);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'position_id' => 'required',
            'role' => 'required'
        ]);
        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function edit(User $user) {
        $positions = Position::all();
        return view('pages.users.form', compact('user', 'positions'));
    }

    public function update(Request $request, User $user) {
        $data = $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'position_id' => 'required',
            'password' => 'nullable',
            'role' => 'required'
        ]);

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }else{
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function getAPIProfile($id) {
        $data = User::with('position')->find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil.',
            'data' => $data
        ]);
    }

    public function updateAPIProfile(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required',
            'password' => 'nullable'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate.',
            'data' => $user
        ]);
    }

    public function getUserAPI(){
        $data = User::all();
        return $data;
    }
}
