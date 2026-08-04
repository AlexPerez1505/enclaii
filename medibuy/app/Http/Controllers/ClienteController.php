<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Seguimiento;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Lista de asesores válidos (centralizada).
     */
    private const ASESORES_VALIDOS = [
        'Jesús Tellez',
        'Gabriela Diaz',
        'Joel Diaz',
        'Anahí Tellez',
        'Jose Alex',
        'Megan Diaz',
        'Victor Guerrero',
    ];

    /**
     * Normaliza un teléfono dejando SOLO los 10 dígitos.
     */
    private function normalizarTelefono(?string $telefono): ?string
    {
        if (!$telefono) return null;
        $solo = preg_replace('/\D+/', '', $telefono);
        return $solo !== '' ? $solo : null;
    }

    public function index()
    {
        $clientes = Cliente::with('categoria')
            ->orderByDesc('created_at')
            ->get();

        $seguimientos = Seguimiento::whereNull('completado')->with('cliente')->get();
        $seguimientosPorCliente = $seguimientos->groupBy('cliente_id');

        $alertasGenerales = $seguimientos->filter(function ($s) {
            $fecha = Carbon::parse($s->fecha_seguimiento)->startOfDay();
            $dias = now()->startOfDay()->diffInDays($fecha, false);
            return $dias <= 7;
        })->map(function ($s) {
            $fecha = Carbon::parse($s->fecha_seguimiento)->startOfDay();
            $dias = now()->startOfDay()->diffInDays($fecha, false);
            return [
                'seguimiento_id' => $s->id,
                'cliente'        => $s->cliente,
                'fecha'          => $s->fecha_seguimiento,
                'dias'           => $dias,
            ];
        });

        return view('clientes.index', compact('clientes', 'seguimientosPorCliente', 'alertasGenerales'));
    }

    public function create()
{
    $categorias = Categoria::all();
    $redirectTo = request('redirect_to', route('clientes.index'));
    return view('clientes.create', compact('categorias', 'redirectTo'));
}

    public function store(Request $request)
    {
        $request->merge([
            'telefono' => $this->normalizarTelefono($request->input('telefono')),
        ]);

        $request->validate([
            'nombre'            => 'required|string|max:255',
            'apellido'          => 'required|string|max:255',
            'telefono'          => 'nullable|digits:10|unique:clientes,telefono',
            'email'             => 'nullable|email|max:255|unique:clientes,email',
            'comentarios'       => 'nullable|string',
            'categoria_id'      => 'nullable|exists:categorias,id',
            'recibe_promocion'  => 'required|boolean',
            'asesor'            => 'nullable|string|max:255',
            'congreso_conocido' => 'nullable|string|max:255',
        ], [
            'telefono.digits'           => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.unique'           => 'El teléfono ya está registrado.',
            'email.unique'              => 'El correo ya está registrado.',
            'recibe_promocion.required' => 'Debes indicar si recibe promoción.',
            'asesor.in'                 => 'El asesor seleccionado no es válido.',
        ]);

        $cliente = Cliente::create([
            'nombre'            => $request->input('nombre'),
            'apellido'          => $request->input('apellido'),
            'telefono'          => $request->input('telefono'),
            'email'             => $request->input('email') ?: null,
            'comentarios'       => $request->input('comentarios'),
            'categoria_id'      => $request->input('categoria_id'),
            'recibe_promocion'  => $request->boolean('recibe_promocion'),
            'asesor'            => $request->input('asesor'),
            'congreso_conocido' => $request->input('congreso_conocido'),
        ]);

        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($wantsJson) {
            return response()->json([
                'success'    => true,
                'message'    => 'Cliente creado exitosamente.',
                'cliente_id' => $cliente->id,
                'cliente'    => [
                    'id'               => $cliente->id,
                    'nombre'           => $cliente->nombre,
                    'apellido'         => $cliente->apellido,
                    'telefono'         => $cliente->telefono,
                    'email'            => $cliente->email,
                    'comentarios'      => $cliente->comentarios,
                    'recibe_promocion' => $cliente->recibe_promocion,
                ],
            ], 201);
        }

        $redirect = $request->input('redirect_to', route('clientes.index'));
        return redirect($redirect)
            ->with('success', 'Cliente creado exitosamente.')
            ->with('cliente_creado', true);
    }

    public function show($id)
    {
        $cliente = Cliente::with(['notas', 'seguimientos', 'categoria'])->findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        $categorias = Categoria::all();
        return view('clientes.edit', compact('cliente', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $request->merge([
            'telefono' => $this->normalizarTelefono($request->input('telefono')),
        ]);

        $request->validate([
            'nombre'            => 'required|string|max:255',
            'apellido'          => 'required|string|max:255',
            'telefono'          => 'nullable|digits:10|unique:clientes,telefono,' . $cliente->id,
            'email'             => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'comentarios'       => 'nullable|string',
            'categoria_id'      => 'nullable|exists:categorias,id',
            'recibe_promocion'  => 'required|boolean',
            'asesor'            => 'nullable|string|in:' . implode(',', self::ASESORES_VALIDOS),
            'congreso_conocido' => 'nullable|string|max:255',
        ], [
            'telefono.digits'           => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.unique'           => 'El teléfono ya está registrado por otro cliente.',
            'email.unique'              => 'El correo ya está registrado por otro cliente.',
            'recibe_promocion.required' => 'Debes indicar si recibe promoción.',
            'asesor.in'                 => 'El asesor seleccionado no es válido.',
        ]);

        $cliente->update([
            'nombre'            => $request->nombre,
            'apellido'          => $request->apellido,
            'telefono'          => $request->telefono,
            'email'             => $request->email ?: null,
            'comentarios'       => $request->comentarios,
            'categoria_id'      => $request->categoria_id,
            'recibe_promocion'  => $request->boolean('recibe_promocion'),
            'congreso_conocido' => $request->input('congreso_conocido'),
            'asesor'            => $request->input('asesor'),
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();
            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar este cliente porque tiene registros relacionados.');
        } catch (\Throwable $e) {
            return redirect()->route('clientes.index')->with('error', 'Ocurrió un error al eliminar el cliente.');
        }
    }

    /**
     * Verifica si el teléfono o email ya existen.
     * Acepta `ignore_id` para excluir al propio cliente en edición.
     */
    public function checkUnique(Request $request)
    {
        $telefono = $this->normalizarTelefono($request->input('telefono'));
        $email    = strtolower(trim((string) $request->input('email')));
        $ignoreId = $request->input('ignore_id');

        $errorTelefono = null;
        $errorEmail    = null;

        if (!empty($telefono)) {
            if (strlen($telefono) !== 10) {
                $errorTelefono = 'El teléfono debe tener 10 dígitos.';
            } else {
                $existeTel = Cliente::where('telefono', $telefono)
                    ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                    ->exists();
                if ($existeTel) $errorTelefono = 'El teléfono ya está registrado.';
            }
        }

        if (!empty($email)) {
            $existeMail = Cliente::where('email', $email)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists();
            if ($existeMail) $errorEmail = 'El correo ya está registrado.';
        }

        return response()->json([
            'success'        => !$errorTelefono && !$errorEmail,
            'error_telefono' => $errorTelefono,
            'error_email'    => $errorEmail,
        ]);
    }

    public function getClients(Request $request)
    {
        $search = $request->input('search');

        $clients = Cliente::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', "%$search%")
                         ->orWhere('apellido', 'like', "%$search%")
                         ->orWhere('telefono', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
        })->get([
            'nombre', 'apellido', 'telefono', 'email', 'comentarios', 'recibe_promocion'
        ]);

        return response()->json($clients);
    }

    public function updateAsesor(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:clientes,id',
            'asesor' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::findOrFail($request->id);
        $cliente->asesor = $request->asesor;
        $cliente->save();

        return response()->json(['message' => 'Asesor actualizado correctamente']);
    }

    public function checkTelefono(Request $request)
    {
        $request->validate([
            'telefono' => 'required|digits:10',
        ]);

        $exists = Cliente::where('telefono', $request->telefono)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['items' => []]);
        }

        $like = '%' . str_replace(' ', '%', $q) . '%';

        $result = Cliente::query()
            ->select(['id','nombre','apellido','telefono','email'])
            ->where(function ($qq) use ($like) {
                $qq->where('nombre', 'like', $like)
                   ->orWhere('apellido', 'like', $like)
                   ->orWhere('telefono', 'like', $like)
                   ->orWhere('email', 'like', $like);
            })
            ->orderByRaw("
                CASE
                  WHEN nombre   LIKE ? THEN 0
                  WHEN apellido LIKE ? THEN 1
                  WHEN telefono LIKE ? THEN 2
                  WHEN email    LIKE ? THEN 3
                  ELSE 4
                END
            ", [$like, $like, $like, $like])
            ->limit(12)
            ->get();

        $items = $result->map(function ($c) {
            $label = trim($c->nombre . ' ' . $c->apellido);
            if ($label === '') $label = 'Cliente sin nombre';

            $sub = array_filter([$c->telefono, $c->email]);

            return [
                'id'    => $c->id,
                'label' => $label,
                'desc'  => implode(' · ', $sub),
            ];
        })->values();

        return response()->json(['items' => $items]);
    }
    public function validarPin(Request $request)
{
    if ($request->pin !== env('APROBACION_PIN')) {
        return response()->json([
            'success' => false
        ], 422);
    }

    return response()->json([
        'success' => true
    ]);
}
}