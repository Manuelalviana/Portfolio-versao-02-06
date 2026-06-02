<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\Situacao;
use App\Models\Tecnologia;

class TecnologiaActivity
{
    public static function isPublishedSituation(?string $nome): bool
    {
        if (! $nome) {
            return false;
        }

        $n = mb_strtolower($nome);

        return str_contains($n, 'publicad')
            || str_contains($n, 'ativo')
            || str_contains($n, 'aprovad');
    }

    public static function logCreated(Tecnologia $tecnologia): void
    {
        $tecnologia->loadMissing('situacao');
        $titulo = $tecnologia->titulo ?? $tecnologia->nome ?? 'Sem título';
        $situacao = $tecnologia->situacao?->nome ?? 'Rascunho';

        ActivityLogger::log(
            ActivityLog::ACTION_TECNOLOGIA_CREATED,
            "Tecnologia criada: {$titulo} (situação: {$situacao})",
            $tecnologia,
            null,
            ['situacao' => $situacao, 'numero_caso' => $tecnologia->numero_caso],
        );
    }

    public static function logUpdated(Tecnologia $tecnologia, ?Situacao $situacaoAnterior): void
    {
        $tecnologia->loadMissing('situacao');
        $titulo = $tecnologia->titulo ?? $tecnologia->nome ?? 'Sem título';
        $novaSituacao = $tecnologia->situacao?->nome;

        $descricao = "Tecnologia editada: {$titulo}";

        if ($situacaoAnterior?->id !== $tecnologia->situacao_id && $novaSituacao) {
            $anterior = $situacaoAnterior?->nome ?? '—';
            $descricao .= " (situação: {$anterior} → {$novaSituacao})";
        }

        ActivityLogger::log(
            ActivityLog::ACTION_TECNOLOGIA_UPDATED,
            $descricao,
            $tecnologia,
        );

        if ($situacaoAnterior?->id !== $tecnologia->situacao_id && self::isPublishedSituation($novaSituacao)) {
            ActivityLogger::log(
                ActivityLog::ACTION_TECNOLOGIA_PUBLISHED,
                "Tecnologia publicada: {$titulo}",
                $tecnologia,
                null,
                ['situacao' => $novaSituacao],
            );
        }
    }

    public static function logDeleted(Tecnologia $tecnologia): void
    {
        $titulo = $tecnologia->titulo ?? $tecnologia->nome ?? 'Sem título';

        ActivityLogger::log(
            ActivityLog::ACTION_TECNOLOGIA_DELETED,
            "Tecnologia excluída: {$titulo}",
            null,
            null,
            ['numero_caso' => $tecnologia->numero_caso],
        );
    }
}
