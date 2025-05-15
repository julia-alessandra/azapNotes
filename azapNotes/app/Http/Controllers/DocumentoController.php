<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with(['titulo', 'departamentos'])
            ->orderBy('data_atualizacao', 'desc')
            ->paginate(10);

        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        return view('documentos.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'conteudo' => 'required|string',
            'versao' => 'required|string|max:20',
            'tipo' => 'required|string|in:procedimento,instrucao,manual,formulario,outro',
            'categoria' => 'required|string|max:100',
            'nivel_acesso' => 'required|string|in:publico,restrito,confidencial',
            'processo_relacionado' => 'nullable|string|max:255',
            'departamentos' => 'required|array',
            'departamentos.*' => 'exists:departamentos,_id',
            'anexos.*' => 'nullable|file|max:10240',
            'referencias' => 'nullable|array',
            'referencias.*' => 'string|max:255',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'observacoes' => 'nullable|string'
        ]);

        $documento = new Documento($request->except('anexos', 'departamentos'));
        $documento->criado_por = Auth::id();
        $documento->atualizado_por = Auth::id();
        $documento->data_criacao = now();
        $documento->data_atualizacao = now();
        $documento->status = 'ativo';

        if ($request->hasFile('anexos')) {
            $anexos = [];
            foreach ($request->file('anexos') as $anexo) {
                $path = $anexo->store('documentos/anexos');
                $anexos[] = [
                    'nome' => $anexo->getClientOriginalName(),
                    'caminho' => $path,
                    'tipo' => $anexo->getMimeType(),
                    'tamanho' => $anexo->getSize()
                ];
            }
            $documento->anexos = $anexos;
        }

        $documento->save();

        $documento->departamentos()->attach($request->departamentos);

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Documento criado com sucesso!');
    }

    public function show(Documento $documento)
    {
        $documento->load(['criador', 'atualizador', 'departamentos']);
        return view('documentos.show', compact('documento'));
    }

    public function edit(Documento $documento)
    {
        $departamentos = Departamento::where('status', 'ativo')->get();
        $documento->load('departamentos');
        return view('documentos.edit', compact('documento', 'departamentos'));
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'conteudo' => 'required|string',
            'versao' => 'required|string|max:20',
            'tipo' => 'required|string|in:procedimento,instrucao,manual,formulario,outro',
            'categoria' => 'required|string|max:100',
            'nivel_acesso' => 'required|string|in:publico,restrito,confidencial',
            'processo_relacionado' => 'nullable|string|max:255',
            'departamentos' => 'required|array',
            'departamentos.*' => 'exists:departamentos,_id',
            'anexos.*' => 'nullable|file|max:10240', // max 10MB
            'referencias' => 'nullable|array',
            'referencias.*' => 'string|max:255',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'observacoes' => 'nullable|string'
        ]);

        $documento->fill($request->except('anexos', 'departamentos'));
        $documento->atualizado_por = Auth::id();
        $documento->data_atualizacao = now();

        if ($request->hasFile('anexos')) {
            $anexos = $documento->anexos ?? [];
            foreach ($request->file('anexos') as $anexo) {
                $path = $anexo->store('documentos/anexos');
                $anexos[] = [
                    'nome' => $anexo->getClientOriginalName(),
                    'caminho' => $path,
                    'tipo' => $anexo->getMimeType(),
                    'tamanho' => $anexo->getSize()
                ];
            }
            $documento->anexos = $anexos;
        }

        $documento->save();

        $documento->departamentos()->sync($request->departamentos);

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Documento atualizado com sucesso!');
    }

    public function destroy(Documento $documento)
    {
        if ($documento->temAnexos()) {
            foreach ($documento->anexos as $anexo) {
                Storage::delete($anexo['caminho']);
            }
        }

        $documento->departamentos()->detach();
        
        $documento->delete();

        return redirect()
            ->route('documentos.index')
            ->with('success', 'Documento removido com sucesso!');
    }

    public function modificaStatus(Documento $documento)
    {
        $documento->status = $documento->status === 'ativo' ? 'inativo' : 'ativo';
        $documento->atualizado_por = Auth::id();
        $documento->data_atualizacao = now();
        $documento->save();

        return redirect()
            ->back()
            ->with('success', 'Status do documento atualizado com sucesso!');
    }

    public function removeAnexo(Documento $documento, int $index)
    {
        if ($documento->temAnexos() && isset($documento->anexos[$index])) {
            $anexo = $documento->anexos[$index];
            Storage::delete($anexo['caminho']);
            
            $anexos = $documento->anexos;
            unset($anexos[$index]);
            $documento->anexos = array_values($anexos);
            
            $documento->atualizado_por = Auth::id();
            $documento->data_atualizacao = now();
            $documento->save();

            return redirect()
                ->back()
                ->with('success', 'Anexo removido com sucesso!');
        }

        return redirect()
            ->back()
            ->with('error', 'Anexo não encontrado!');
    }

    public function aprovar(Documento $documento)
    {
        $documento->status = 'aprovado';
        $documento->aprovado_por = Auth::id();
        $documento->data_aprovacao = now();
        $documento->atualizado_por = Auth::id();
        $documento->data_atualizacao = now();
        $documento->save();

        return redirect()
            ->back()
            ->with('success', 'Documento aprovado com sucesso!');
    }

    public function revisar(Documento $documento)
    {
        $documento->status = 'em_revisao';
        $documento->atualizado_por = Auth::id();
        $documento->data_atualizacao = now();
        $documento->data_revisao = now();
        $documento->save();

        return redirect()
            ->back()
            ->with('success', 'Documento enviado para revisão!');
    }

    public function porCategoria(Request $request, string $categoria)
    {
        $documentos = Documento::getPorCategoria($categoria)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('documentos.por-categoria', compact('documentos', 'categoria'));
    }

    public function porDepartamento(Departamento $departamento)
    {
        $documentos = Documento::getPorDepartamento($departamento->_id)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('documentos.por-departamento', compact('documentos', 'departamento'));
    }

    public function porTipo(Request $request, string $tipo)
    {
        $documentos = Documento::getPorTipo($tipo)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('documentos.por-tipo', compact('documentos', 'tipo'));
    }

    public function porNivelAcesso(Request $request, string $nivel)
    {
        $documentos = Documento::getPorNivelAcesso($nivel)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('documentos.por-nivel', compact('documentos', 'nivel'));
    }

    public function porTag(Request $request, string $tag)
    {
        $documentos = Documento::getPorTag($tag)
            ->with(['criador', 'departamentos'])
            ->paginate(10);

        return view('documentos.por-tag', compact('documentos', 'tag'));
    }

    public function recentes()
    {
        $documentos = Documento::getRecentes()
            ->with(['criador', 'departamentos'])
            ->get();

        return view('documentos.recentes', compact('documentos'));
    }

    public function busca(Request $request)
    {
        $request->validate([
            'termo' => 'required|string|min:3',
            'tipo' => 'nullable|string',
            'categoria' => 'nullable|string',
            'nivel_acesso' => 'nullable|string',
            'departamento' => 'nullable|exists:departamentos,_id',
            'status' => 'nullable|string'
        ]);

        $query = Documento::query()
            ->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->termo}%")
                    ->orWhere('descricao', 'like', "%{$request->termo}%")
                    ->orWhere('conteudo', 'like', "%{$request->termo}%")
                    ->orWhere('tags', 'like', "%{$request->termo}%")
                    ->orWhere('processo_relacionado', 'like', "%{$request->termo}%");
            });

        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->nivel_acesso) {
            $query->where('nivel_acesso', $request->nivel_acesso);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->departamento) {
            $query->whereHas('departamentos', function ($q) use ($request) {
                $q->where('_id', $request->departamento);
            });
        }

        $documentos = $query->with(['criador', 'departamentos'])
            ->orderBy('data_atualizacao', 'desc')
            ->paginate(10);

        return view('documentos.busca', compact('documentos', 'request'));
    }
}
