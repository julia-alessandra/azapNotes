<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Link extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'links';

    protected $fillable = [
        'titulo',
        'url',
        'descricao',
        'categoria',
        'status',
        'criado_por',
        'atualizado_por',
        'data_publicacao'
    ];

    protected $casts = [
        'data_publicacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Departamento::class,
            'link_departamento',
            'link_id',
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

    public function getDataPublicacaoFormatada(): string
    {
        return $this->data_publicacao->format('d/m/Y');
    }

    public static function getRecentes(int $limite = 5): BelongsToMany
    {
        return static::where('status', 'ativo')
            ->orderBy('data_publicacao', 'desc')
            ->limit($limite);
    }
}
