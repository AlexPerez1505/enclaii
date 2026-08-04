<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductCatalogTools
{
    /**
     * Busca productos por texto o filtros.
     * args:
     *  q, marca, tipo_equipo, modelo, limit
     */
    public function searchProducts(array $args): array
    {
        $q          = trim((string)($args['q'] ?? ''));
        $marca      = trim((string)($args['marca'] ?? ''));
        $tipoEquipo = trim((string)($args['tipo_equipo'] ?? ''));
        $modelo     = trim((string)($args['modelo'] ?? ''));
        $limit      = (int)($args['limit'] ?? 5);
        $limit      = max(1, min(10, $limit));

        $query = DB::table('productos')
            ->select(['id', 'tipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen']);

        if ($marca !== '') {
            $query->where('marca', 'like', '%'.$this->likeEscape($marca).'%');
        }
        if ($tipoEquipo !== '') {
            $query->where('tipo_equipo', 'like', '%'.$this->likeEscape($tipoEquipo).'%');
        }
        if ($modelo !== '') {
            $query->where('modelo', 'like', '%'.$this->likeEscape($modelo).'%');
        }

        if ($q !== '') {
            $qEsc = $this->likeEscape($q);
            $query->where(function ($qq) use ($qEsc, $q) {
                // si escribe "id 12" o "12"
                if (ctype_digit($q)) {
                    $qq->orWhere('id', (int)$q);
                }

                $qq->orWhere('tipo_equipo', 'like', '%'.$qEsc.'%')
                   ->orWhere('modelo', 'like', '%'.$qEsc.'%')
                   ->orWhere('marca', 'like', '%'.$qEsc.'%');
            });
        }

        // Orden: primero con stock, luego más stock, luego id desc
        $rows = $query
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END ASC')
            ->orderByDesc('stock')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'count' => $rows->count(),
            'items' => $rows->map(fn($r) => $this->present($r))->values()->all(),
        ];
    }

    /** Obtiene 1 producto por id */
    public function getProductById(array $args): array
    {
        $id = (int)($args['id'] ?? 0);
        if ($id <= 0) return ['found' => false, 'item' => null];

        $row = DB::table('productos')
            ->select(['id', 'tipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen'])
            ->where('id', $id)
            ->first();

        return [
            'found' => (bool)$row,
            'item'  => $row ? $this->present($row) : null,
        ];
    }

    /** Top productos con mayor stock */
    public function listTopInStock(array $args): array
    {
        $limit = (int)($args['limit'] ?? 8);
        $limit = max(1, min(12, $limit));

        $rows = DB::table('productos')
            ->select(['id', 'tipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen'])
            ->orderByDesc('stock')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'count' => $rows->count(),
            'items' => $rows->map(fn($r) => $this->present($r))->values()->all(),
        ];
    }

    private function present($r): array
    {
        return [
            'id'         => (int)($r->id ?? 0),
            'tipo_equipo'=> (string)($r->tipo_equipo ?? ''),
            'modelo'     => (string)($r->modelo ?? ''),
            'marca'      => (string)($r->marca ?? ''),
            'precio'     => is_null($r->precio) ? null : (float)$r->precio,
            'stock'      => is_null($r->stock) ? null : (int)$r->stock,
            'imagen'     => (string)($r->imagen ?? ''),
        ];
    }

    private function likeEscape(string $s): string
    {
        // Escape básico para LIKE
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $s);
    }
}
