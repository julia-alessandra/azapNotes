<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Departamento extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'departamentos';


    protected $fillable = [
        'nome',
        'descricao',
        'responsavel_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function Users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_departamento',
            'departamento_id',
            'user_id'
        )->withTimestamps();
    }

    public function documentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Documento::class,
            'documento_departamento',
            'departamento_id',
            'documento_id'
        )->withTimestamps();
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function temFuncionarios(): bool
    {
        return $this->users()->count() > 0;
    }

    public function temDocumentos(): bool
    {
        return $this->documentos()->count() > 0;
    }

    public function funcionariosAtivos(): BelongsToMany
    {
        return $this->users()->where('status', 'ativo');
    }

    public function documentosAtivos(): BelongsToMany
    {
        return $this->documentos()->where('status', 'ativo');
    }
}
