<?php

namespace App\Services;

class AsistenciaAiService
{
    public function __construct(
        private readonly OpenAiClient $client,
        private readonly string $primaryModel,
        private readonly array $fallbackModels,
    ) {}

    /**
     * @param array $usuarios  [['id'=>1,'nomina'=>'101224','name'=>'José ...'], ...]
     * @param array $noEncontrados [['excel_id'=>'9','excel_name'=>'Adrian'], ...]
     * @return array excel_id => user_id
     */
    public function matchUsuariosPorNombre(array $usuarios, array $noEncontrados): array
    {
        if (empty($noEncontrados)) return [];

        $usuariosCompact = array_map(fn($u) => [
            'id'     => (int)($u['id'] ?? 0),
            'nomina' => (string)($u['nomina'] ?? ''),
            'name'   => (string)($u['name'] ?? ''),
        ], $usuarios);

        $payload = [
            'usuarios' => $usuariosCompact,
            'excel'    => $noEncontrados,
            'reglas'   => [
                'usar_match_por_nombre' => true,
                'ignorar_acentos'       => true,
                'ignorar_mayusculas'    => true,
                'si_hay_duda'           => 'no_asignar',
            ],
        ];

        $instructions = <<<TXT
Eres un asistente que hace matching de empleados de un Excel contra la tabla users.
Devuelve SOLO JSON válido con la forma:
{"matches":[{"excel_id":"...","user_id":123,"confidence":0.0-1.0}, ...]}
- Solo asigna si el nombre coincide claramente.
- Si no estás seguro, NO lo incluyas.
TXT;

        $input = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $models = array_values(array_unique(array_merge([$this->primaryModel], $this->fallbackModels)));

        $lastErr = null;
        foreach ($models as $model) {
            try {
                $text = $this->client->responses($model, $instructions, $input);

                $json = json_decode($text, true);
                if (!is_array($json) || !isset($json['matches']) || !is_array($json['matches'])) {
                    throw new \RuntimeException("Respuesta no válida (no JSON schema).");
                }

                $map = [];
                foreach ($json['matches'] as $m) {
                    if (!isset($m['excel_id'], $m['user_id'], $m['confidence'])) continue;
                    if ((float)$m['confidence'] < 0.75) continue;
                    $map[(string)$m['excel_id']] = (int)$m['user_id'];
                }

                return $map;
            } catch (\Throwable $e) {
                $lastErr = $e;
            }
        }

        throw $lastErr ?: new \RuntimeException("No se pudo obtener respuesta de OpenAI.");
    }
}
