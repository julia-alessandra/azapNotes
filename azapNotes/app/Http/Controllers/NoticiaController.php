<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;
use App\Models\Departamento;


class NoticiaController extends Controller
{

    public function index()
    {
        $noticias = Noticia::orderBy('data_publicacao', 'desc')->get();
        return view('noticias.index', compact('noticias'));
    }

    public function create()
    {
        return view('noticias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|in:geral,tecnologia,eventos',
            'status' => 'required|string|in:rascunho,publicado',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'categoria' => $request->categoria,
            'status' => $request->status,
            'data_publicacao' => now(),
            'data_atualizacao' => now(),
        ];

        $noticia = Noticia::create($data);

        return redirect()->route('noticias.index')
            ->with('success', 'Notícia criada com sucesso!');
    }

    public function show(Noticia $noticia)
    {
        $noticia = Noticia::findOrFail($noticia->id);
        return view('noticias.show', compact('noticia'));
    }

    public function edit(Noticia $noticia)
    {
        $noticia = Noticia::findOrFail($noticia->id);
        return view('noticias.edit', compact('noticia'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|in:geral,tecnologia,eventos',
            'status' => 'required|string|in:rascunho,publicado',
        ]);

        $noticia = Noticia::findOrFail($noticia->id);
        
        $data = [
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'categoria' => $request->categoria,
            'status' => $request->status,
            'data_atualizacao' => now(),
        ];

        $noticia->update($data);

        return redirect()->route('noticias.index')
            ->with('success', 'Notícia atualizada com sucesso!');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();
        
        return redirect()->route('noticias.index')
            ->with('success', 'Noticia excluída com sucesso!');
    }

    public function modificaStatus(Noticia $noticia)
    {
        $novoStatus = $noticia->status === 'publicado' ? 'rascunho' : 'publicado';
        $noticia->update([
            'status' => $novoStatus,
            'data_atualizacao' => now()
        ]);

        $mensagem = $novoStatus === 'publicado' ? 'publicada' : 'movida para rascunho';
        return redirect()->route('noticias.show', $noticia)
            ->with('success', "Notícia foi {$mensagem} com sucesso");
    }

    public function porCategoria(Request $request, $categoria)
    {
        $request->validate([
            'categoria' => 'required|string|in:geral,tecnologia,eventos'
        ]);

        $noticias = Noticia::where('categoria', $categoria)
            ->where('status', 'publicado')
            ->orderBy('data_publicacao', 'desc')
            ->get();

        return view('noticias.por-categoria', compact('noticias', 'categoria'));
    }

    public function busca(Request $request)
    {
        $request->validate([
            'termo' => 'required|string|min:3',
            'categoria' => 'nullable|string|in:geral,tecnologia,eventos',
            'status' => 'nullable|string|in:rascunho,publicado',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio'
        ]);

        $query = Noticia::query();

        if ($request->filled('termo')) {
            $termo = $request->termo;
            $query->where(function($q) use ($termo) {
                $q->where('titulo', 'like', "%{$termo}%")
                  ->orWhere('conteudo', 'like', "%{$termo}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_publicacao', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_publicacao', '<=', $request->data_fim);
        }

        $noticias = $query->orderBy('data_publicacao', 'desc')->get();

        return view('noticias.busca', compact('noticias', 'request'));
    }

    public function estatisticas()
    {
        $totalNoticias = Noticia::count();
        $noticiasPublicadas = Noticia::where('status', 'publicado')->count();
        $noticiasRascunho = Noticia::where('status', 'rascunho')->count();
        
        $noticiasPorCategoria = Noticia::selectRaw('categoria, count(*) as total')
            ->groupBy('categoria')
            ->get();

        $noticiasPorMes = Noticia::selectRaw('MONTH(data_publicacao) as mes, count(*) as total')
            ->whereYear('data_publicacao', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('noticias.estatisticas', compact(
            'totalNoticias',
            'noticiasPublicadas',
            'noticiasRascunho',
            'noticiasPorCategoria',
            'noticiasPorMes'
        ));
    }

    public function recentes()
    {
        $noticias = Noticia::where('status', 'publicado')
            ->orderBy('data_publicacao', 'desc')
            ->take(5)
            ->get();

        return view('noticias.recentes', compact('noticias'));
    }

    public function relacionadas(Noticia $noticia)
    {
        $noticiasRelacionadas = Noticia::where('categoria', $noticia->categoria)
            ->where('_id', '!=', $noticia->_id)
            ->where('status', 'publicado')
            ->orderBy('data_publicacao', 'desc')
            ->take(3)
            ->get();

        return view('noticias.relacionadas', compact('noticia', 'noticiasRelacionadas'));
    }

    public function porDepartamento(Departamento $departamento)
    {
        $noticias = Noticia::whereHas('departamentos', function($query) use ($departamento) {
                $query->where('departamentos._id', $departamento->_id);
            })
            ->where('status', 'publicado')
            ->orderBy('data_publicacao', 'desc')
            ->paginate(10);

        return view('noticias.por-departamento', compact('noticias', 'departamento'));
    }
}
