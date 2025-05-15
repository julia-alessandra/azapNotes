<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Departamento;

class LinkController extends Controller
{

    public function index()
    {
        $links = Link::with('departamento')
            ->orderBy('data_publicacao', 'desc')
            ->get();
        return view('links.index', compact('links'));
    }

    public function create()
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        return view('links.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:255',
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria' => 'required|string|in:comunicacao,producao',
            'departamento_id' => 'required|exists:departamentos,_id'
        ]);

        $data = [
            'url' => $request->url,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'departamento_id' => $request->departamento_id,
            'data_publicacao' => now()
        ];

        $link = Link::create($data);

        return redirect()->route('links.index')
            ->with('success', 'Link criado com sucesso!');
    }

    public function show(Link $link)
    {
        $link->load('departamento');
        return view('links.show', compact('link'));
    }

    public function edit(Link $link)
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        return view('links.edit', compact('link', 'departamentos'));
    }

    public function update(Request $request, Link $link)
    {
        $request->validate([
            'url' => 'required|url|max:255',
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria' => 'required|string|in:comunicacao,producao',
            'departamento_id' => 'required|exists:departamentos,_id'
        ]);

        $data = [
            'url' => $request->url,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'departamento_id' => $request->departamento_id
        ];

        $link->update($data);

        return redirect()->route('links.index')
            ->with('success', 'Link atualizado com sucesso!');
    }

    public function destroy(Link $link)
    {
        $link->delete();
        return redirect()->route('links.index')
            ->with('success', 'Link excluído com sucesso!');
    }

    public function porDepartamento(Departamento $departamento)
    {
        $links = Link::where('departamento_id', $departamento->_id)
            ->orderBy('data_publicacao', 'desc')
            ->paginate(10);

        return view('links.por-departamento', compact('links', 'departamento'));
    }

    public function porCategoria(Request $request, $categoria)
    {
        $request->validate([
            'categoria' => 'required|string|in:comunicacao,producao'
        ]);

        $links = Link::where('categoria', $categoria)
            ->orderBy('data_publicacao', 'desc')
            ->paginate(10);

        return view('links.por-categoria', compact('links', 'categoria'));
    }

    public function busca(Request $request)
    {
        $request->validate([
            'termo' => 'required|string|min:3',
            'categoria' => 'nullable|string|in:comunicacao,producao',
            'departamento_id' => 'nullable|exists:departamentos,_id'
        ]);

        $query = Link::query();

        if ($request->filled('termo')) {
            $termo = $request->termo;
            $query->where(function($q) use ($termo) {
                $q->where('nome', 'like', "%{$termo}%")
                  ->orWhere('descricao', 'like', "%{$termo}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->departamento_id);
        }

        $links = $query->with('departamento')
            ->orderBy('data_publicacao', 'desc')
            ->paginate(10);

        $departamentos = Departamento::where('status', 'ativo')->get();

        return view('links.busca', compact('links', 'departamentos', 'request'));
    }
    
}
