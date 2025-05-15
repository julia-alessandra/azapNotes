<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TutorialController extends Controller
{
    public function index()
    {
        $tutoriais = Tutorial::with(['criador', 'departamentos'])
            ->orderBy('data_atualizacao', 'desc')
            ->paginate(10);

        return view('tutoriais.index', compact('tutoriais'));
    }

    public function create()
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        return view('tutoriais.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'nivel_dificuldade' => 'required|string|in:iniciante,intermediario,avancado',
            'departamentos' => 'required|array',
            'departamentos.*' => 'exists:departamentos,_id',
            'anexos.*' => 'nullable|file|max:10240', // max 10MB
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'observacoes' => 'nullable|string'
        ]);

        $tutorial = new Tutorial($request->except('anexos', 'departamentos'));
        $tutorial->criado_por = Auth::id();
        $tutorial->atualizado_por = Auth::id();
        $tutorial->data_criacao = now();
        $tutorial->data_atualizacao = now();
        $tutorial->status = 'ativo';

        if ($request->hasFile('anexos')) {
            $anexos = [];
            foreach ($request->file('anexos') as $anexo) {
                $path = $anexo->store('tutoriais/anexos');
                $anexos[] = [
                    'nome' => $anexo->getClientOriginalName(),
                    'caminho' => $path,
                    'tipo' => $anexo->getMimeType(),
                    'tamanho' => $anexo->getSize()
                ];
            }
            $tutorial->anexos = $anexos;
        }

        $tutorial->save();

        $tutorial->departamentos()->attach($request->departamentos);

        return redirect()
            ->route('tutoriais.show', $tutorial)
            ->with('success', 'Tutorial criado com sucesso!');
    }

    public function show(Tutorial $tutorial)
    {
        $tutorial->load(['criador', 'atualizador', 'departamentos']);
        return view('tutoriais.show', compact('tutorial'));
    }

    public function edit(Tutorial $tutorial)
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        $tutorial->load('departamentos');
        return view('tutoriais.edit', compact('tutorial', 'departamentos'));
    }

    public function update(Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'nivel_dificuldade' => 'required|string|in:iniciante,intermediario,avancado',
            'departamentos' => 'required|array',
            'departamentos.*' => 'exists:departamentos,_id',
            'anexos.*' => 'nullable|file|max:10240', // max 10MB
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'observacoes' => 'nullable|string'
        ]);

        $tutorial->fill($request->except('anexos', 'departamentos'));
        $tutorial->atualizado_por = Auth::id();
        $tutorial->data_atualizacao = now();

        if ($request->hasFile('anexos')) {
            $anexos = $tutorial->anexos ?? [];
            foreach ($request->file('anexos') as $anexo) {
                $path = $anexo->store('tutoriais/anexos');
                $anexos[] = [
                    'nome' => $anexo->getClientOriginalName(),
                    'caminho' => $path,
                    'tipo' => $anexo->getMimeType(),
                    'tamanho' => $anexo->getSize()
                ];
            }
            $tutorial->anexos = $anexos;
        }

        $tutorial->save();

        $tutorial->departamentos()->sync($request->departamentos);

        return redirect()
            ->route('tutoriais.show', $tutorial)
            ->with('success', 'Tutorial atualizado com sucesso!');
    }

    public function destroy(Tutorial $tutorial)
    {
        if ($tutorial->temAnexos()) {
            foreach ($tutorial->anexos as $anexo) {
                Storage::delete($anexo['caminho']);
            }
        }

        $tutorial->departamentos()->detach();
        
        $tutorial->delete();

        return redirect()
            ->route('tutoriais.index')
            ->with('success', 'Tutorial removido com sucesso!');
    }

    public function modificaStatus(Tutorial $tutorial)
    {
        $tutorial->status = $tutorial->status === 'ativo' ? 'inativo' : 'ativo';
        $tutorial->atualizado_por = Auth::id();
        $tutorial->data_atualizacao = now();
        $tutorial->save();

        return redirect()
            ->back()
            ->with('success', 'Status do tutorial atualizado com sucesso!');
    }

    public function removeAnexo(Tutorial $tutorial, int $index)
    {
        if ($tutorial->temAnexos() && isset($tutorial->anexos[$index])) {
            $anexo = $tutorial->anexos[$index];
            Storage::delete($anexo['caminho']);
            
            $anexos = $tutorial->anexos;
            unset($anexos[$index]);
            $tutorial->anexos = array_values($anexos);
            
            $tutorial->atualizado_por = Auth::id();
            $tutorial->data_atualizacao = now();
            $tutorial->save();

            return redirect()
                ->back()
                ->with('success', 'Anexo removido com sucesso!');
        }

        return redirect()
            ->back()
            ->with('error', 'Anexo não encontrado!');
    }

    public function porCategoria(Request $request, string $categoria)
    {
        $tutoriais = Tutorial::getPorCategoria($categoria)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('tutoriais.por-categoria', compact('tutoriais', 'categoria'));
    }

    public function porDepartamento(Departamento $departamento)
    {
        $tutoriais = Tutorial::getPorDepartamento($departamento->_id)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('tutoriais.por-departamento', compact('tutoriais', 'departamento'));
    }

    public function porTag(Request $request, string $tag)
    {
        $tutoriais = Tutorial::getPorTag($tag)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('tutoriais.por-tag', compact('tutoriais', 'tag'));
    }

    public function recentes()
    {
        $tutoriais = Tutorial::getRecentes()
            ->with(['criador', 'departamentos'])
            ->get();

        return view('tutoriais.recentes', compact('tutoriais'));
    }

    public function busca(Request $request)
    {
        $request->validate([
            'termo' => 'required|string|min:3',
            'categoria' => 'nullable|string',
            'nivel' => 'nullable|string',
            'departamento' => 'nullable|exists:departamentos,_id'
        ]);

        $query = Tutorial::query()
            ->where('status', 'ativo')
            ->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->termo}%")
                    ->orWhere('descricao', 'like', "%{$request->termo}%")
                    ->orWhere('conteudo', 'like', "%{$request->termo}%")
                    ->orWhere('tags', 'like', "%{$request->termo}%");
            });

        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->nivel) {
            $query->where('nivel_dificuldade', $request->nivel);
        }

        if ($request->departamento) {
            $query->whereHas('departamentos', function ($q) use ($request) {
                $q->where('_id', $request->departamento);
            });
        }

        $tutoriais = $query->with(['criador', 'departamentos'])
            ->orderBy('data_atualizacao', 'desc')
            ->paginate(10);

        return view('tutoriais.busca', compact('tutoriais', 'request'));
    }
} 