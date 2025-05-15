<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Noticia extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'noticias';

    protected $fillable = [
        'titulo',
        'conteudo',
        'categoria',
        'status',
        'criado_por',
        'atualizado_por',
        'data_publicacao'
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'data_publicacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Departamento::class,
            'noticia_departamento',
            'noticia_id',
            'departamento_id'
        )->withTimestamps();
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function temDepartamentos(): bool
    {
        return $this->departamentos()->count() > 0;
    }

    public function getResumo(int $tamanho = 150): string
    {
        return str()->limit(strip_tags($this->conteudo), $tamanho);
    }

    public function getDataPublicacaoFormatada(): string
    {
        return $this->data_publicacao->format('d/m/Y');
    }

    public static function getRecentes(int $limite = 5): BelongsToMany
    {
        return static::where('status', 'publicada')
            ->orderBy('data_publicacao', 'desc')
            ->limit($limite);
    }
}
