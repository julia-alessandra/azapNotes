<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MongoDB\Laravel\Auth\User;

class Tutorial extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'tutoriais';

    protected $fillable = [
        'titulo',
        'descricao',
        'conteudo',
        'categoria',
        'status',
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
        'created_at' => 'datetime',
    ];

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Departamento::class,
            'tutorial_departamento',
            'tutorial_id',
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
} 