<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Documento extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'documentos';

    protected $fillable = [
        'titulo',
        'descricao',
        'conteudo',
        'versao',
        'status',
        'tipo',
        'categoria',
        'nivel_acesso',
        'criado_por',
        'anexos',
        'tags',
        'observacoes',
        'data_criacao',
    ];

    protected $casts = [
        'anexos' => 'array',
        'tags' => 'array',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'data_revisao' => 'datetime',
        'data_aprovacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Departamento::class,
            'documento_departamento',
            'documento_id',
            'departamento_id'
        )->withTimestamps();
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function temDepartamentos(): bool
    {
        return $this->departamentos()->count() > 0;
    }

    public function temAnexos(): bool
    {
        return !empty($this->anexos);
    }

    public function temTags(): bool
    {
        return !empty($this->tags);
    }

    public function getResumo(int $tamanho = 150): string
    {
        return str()->limit(strip_tags($this->descricao), $tamanho);
    }

    public function getDataCriacaoFormatada(): string
    {
        return $this->data_criacao->format('d/m/Y');
    }

    public static function getPendentesAprovacao(): BelongsToMany
    {
        return static::where('status', 'pendente')
            ->orderBy('data_atualizacao', 'desc');
    }

    public static function getAprovados(): BelongsToMany
    {
        return static::where('status', 'aprovado')
            ->orderBy('data_aprovacao', 'desc');
    }
}