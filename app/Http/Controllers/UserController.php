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
            'position_id' => 'required'
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
            'position_id' => 'required'
        ]);
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
