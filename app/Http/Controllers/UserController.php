<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.usuarios', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.usuarios.show', compact('user'));
    }

    public function toggleAdmin(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('success', 'Você não pode alterar sua própria permissão.');
        }

        $user->update([
            'is_admin' => ! (bool) $user->getRawOriginal('is_admin'),
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Permissão atualizada com sucesso.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('success', 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário removido com sucesso.');
    }
}