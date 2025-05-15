<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;

use function Laravel\Prompts\alert;

class DepartamentoController extends Controller
{

    public function index()
    {
        $departamentos = Departamento::with(['responsavel', 'users'])->get();
        return view('departamentos.index', compact('departamentos'));
    }


    public function create()
    {
        $users = User::where('status', 'ativo')->get();
        return view('departamentos.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'responsavel_id' => 'required|exists:users,_id',
            'status' => 'required|in:ativo,inativo'
        ]);

        $departamento = Departamento::create($request->all());

        return redirect()->route('departamentos.index')
            ->with('success', 'Departamento criado com sucesso!');
    }

    public function show(Departamento $departamento)
    {
        $departamento->load(['responsavel', 'users']);
        return view('departamentos.show', compact('departamento'));
    }

    public function edit(Departamento $departamento)
    {
        $users = User::where('status', 'ativo')->get();
        return view('departamentos.edit', compact('departamento', 'users'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'responsavel_id' => 'required|exists:users,_id',
            'status' => 'required|in:ativo,inativo'
        ]);

        $departamento->update($request->all());

        return redirect()->route('departamentos.index')
            ->with('success', 'Departamento atualizado com sucesso!');
    }


    public function destroy(Departamento $departamento)
    {
        if ($departamento->users()->where('status', 'ativo')->count() > 0) {
            return redirect()->route('departamentos.index')
                ->with('error', 'Não é possível excluir um departamento que possui funcionários ativos.');
        }

        $departamento->delete();

        return redirect()->route('departamentos.index')
            ->with('success', 'Departamento excluído com sucesso!');
    }

    public function removeResponsavel(Departamento $departamento, User $user)
    {
        if ($departamento->responsavel_id !== $user->_id) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "{$user->nome} não é responsável pelo departamento {$departamento->nome}");
        }

        $usersAtivos = $departamento->users()
            ->where('status', 'ativo')
            ->where('_id', '!=', $user->_id)
            ->count();

        if ($usersAtivos === 0) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', 'Não é possível remover o responsável pois não há outros funcionários ativos no departamento');
        }

        $departamento->update(['responsavel_id' => null]);

        return redirect()->route('departamentos.show', $departamento)
            ->with('success', "{$user->nome} foi removido como responsável do departamento {$departamento->nome}");
    }

    public function removeUser(Departamento $departamento, User $user)
    {
        if ($departamento->responsavel_id === $user->_id) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "Não é possível remover {$user->nome} pois é o responsável do departamento.");
        }

        if ($departamento->users()->count() === 1 && $departamento->status === 'ativo') {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', 'Não é possível remover o último funcionário de um departamento ativo');
        }

        $user->update(['departamento_id' => null]);

        return redirect()->route('departamentos.show', $departamento)
            ->with('success', "{$user->nome} foi removido do departamento {$departamento->nome}");
    }

    public function addUser(Request $request, Departamento $departamento)
    {
        $request->validate([
            'user_id' => 'required|exists:users,_id'
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->departamento_id) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "{$user->nome} já pertence a outro departamento");
        }

        if ($user->status !== 'ativo') {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "Não é possível adicionar um funcionário inativo ao departamento");
        }

        $user->update(['departamento_id' => $departamento->_id]);

        return redirect()->route('departamentos.show', $departamento)
            ->with('success', "{$user->nome} foi adicionado ao departamento {$departamento->nome}");
    }

    public function mudaResponsavel(Request $request, Departamento $departamento)
    {
        $request->validate([
            'user_id' => 'required|exists:users,_id'
        ]);

        $user = user::findOrFail($request->user_id);

        if ($user->departamento_id !== $departamento->_id) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "{$user->nome} não pertence a este departamento");
        }

        if ($user->status !== 'ativo') {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', "Não é possível definir um funcionário inativo como responsável");
        }

        $departamento->update(['responsavel_id' => $user->_id]);

        return redirect()->route('departamentos.show', $departamento)
            ->with('success', "{$user->nome} foi definido como responsável do departamento {$departamento->nome}");
    }

    public function modificaStatus(Departamento $departamento)
    {
        if ($departamento->status === 'ativo' && $departamento->users()->where('status', 'ativo')->count() > 0) {
            return redirect()->route('departamentos.show', $departamento)
                ->with('error', 'Não é possível inativar um departamento que possui funcionários ativos');
        }

        $novoStatus = $departamento->status === 'ativo' ? 'inativo' : 'ativo';
        $departamento->update(['status' => $novoStatus]);

        $mensagem = $novoStatus === 'ativo' ? 'ativado' : 'inativado';
        return redirect()->route('departamentos.show', $departamento)
            ->with('success', "Departamento {$departamento->nome} foi {$mensagem} com sucesso");
    }
}