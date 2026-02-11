<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mongodb.users,email',
            'password' => 'required|string|min:6|confirmed',
            'permission_level' => 'required|integer|in:1,2,3',
        ], [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Email inválido',
            'email.unique' => 'Este email já está cadastrado',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas não conferem',
            'permission_level.required' => 'Selecione um nível de permissão',
            'permission_level.in' => 'Nível de permissão inválido',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'permission_level' => $request->permission_level,
            'active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit($id)
    {
        if (Auth::user()->permission_level !== User::PERMISSION_ADMIN) {
            abort(403, 'Somente o Administrador pode editar usuários.');
        }
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->permission_level !== User::PERMISSION_ADMIN) {
            abort(403, 'Somente o Administrador pode editar usuários.');
        }
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mongodb.users,email,' . $id,
            'permission_level' => 'required|integer|in:1,2,3',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Email inválido',
            'email.unique' => 'Este email já está cadastrado',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas não conferem',
            'permission_level.required' => 'Selecione um nível de permissão',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'permission_level' => $request->permission_level,
            'active' => $request->has('active'),
        ];
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        if (Auth::user()->permission_level !== User::PERMISSION_ADMIN) {
            abort(403, 'Somente o Administrador pode excluir usuários.');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
