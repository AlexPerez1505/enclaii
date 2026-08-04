<?php
// app/Http/Controllers/CashTransactionController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest; // si tienes FormRequests adicionales
use App\Jobs\GenerateTransactionReceipt;
use App\Models\CashTransaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CashTransactionController extends Controller
{
    /**
     * Dominio fijo para los enlaces públicos del QR.
     * Si algún día cambia, edítalo aquí.
     */
    private const QR_BASE = 'https://medibuy.grupomedibuy.com';

    /** ---------------- VISTAS ---------------- */

    // Dashboard con filtros (gráfica, KPIs, tabla) - AJAX
    public function index(Request $r)
{
    // Si no vienen fechas, usa el primer movimiento como 'from'
    $first = CashTransaction::min('created_at');
    $defaultFrom = $first ? \Carbon\Carbon::parse($first)->toDateString()
                          : now()->subDays(30)->toDateString();

    $filters = [
        'from'       => $r->input('from') ?: $defaultFrom,
        'to'         => $r->input('to')   ?: now()->toDateString(),
        'type'       => $r->input('type'),
        'manager_id' => $r->input('manager_id'),
        'user_id'    => $r->input('user_id'),
        // nuevo: per_page UI (opcional, default 20)
        'per_page'   => (int) $r->input('per_page', 20),
    ];

    return view('transactions.index', [
        'filters'  => $filters,
        'managers' => User::where('role','admin')->orderBy('name')->get(['id','name']),
        'people'   => User::orderBy('name')->get(['id','name']),
    ]);
}

    // Vista "tabs" SPA (3 pestañas en una sola pantalla)
    public function create()
    {
        return view('transactions.create-tabs', [
            'managers' => User::where('role','admin')->orderBy('name')->get(['id','name']),
            'people'   => User::orderBy('name')->get(['id','name']),
            'now'      => now()->format('Y-m-d\TH:i'),
        ]);
    }

    /** ---------------- ACCIONES POR PESTAÑA (AJAX) ---------------- */

    // TAB 1: "Mi jefa me da dinero" (allocation)
    public function storeAllocation(Request $req)
    {
        $req->validate([
            'manager_id'          => ['required','exists:users,id'],
            'counterparty_id'     => ['required','exists:users,id'],
            'amount'              => ['required','numeric','min:0.01'],
            'purpose'             => ['nullable','string','max:255'],
            'performed_at'        => ['nullable','date'],
            'manager_signature'   => ['required','string'], // tu firma (canvas base64)
        ]);

        // Ambos deben ser ADMIN
        $encargado = User::findOrFail($req->manager_id);
        $jefa      = User::findOrFail($req->counterparty_id);
        if ($encargado->role !== 'admin' || $jefa->role !== 'admin') {
            abort(422, 'Encargado y contraparte deben ser ADMIN.');
        }

        return DB::transaction(function () use ($req) {
            $mgrSig    = $this->storeDataUrl($req->manager_signature, 'signatures');
            $createdAt = $req->performed_at ? Carbon::parse($req->performed_at) : now();

            $trx = CashTransaction::create([
                'type'                        => 'allocation',
                'manager_id'                  => $req->manager_id,
                'counterparty_id'             => $req->counterparty_id,
                'amount'                      => $req->amount,
                'purpose'                     => $req->purpose,
                'manager_signature_path'      => $mgrSig,
                'counterparty_signature_path' => null, // no firma jefa
                'created_by'                  => auth()->id(),
                'created_at'                  => $createdAt,
                'updated_at'                  => $createdAt,
            ]);

            // Lo puedes dejar en cola; receipt() genera on-demand si hace falta.
            GenerateTransactionReceipt::dispatch($trx->id);

            return response()->json(['ok'=>true, 'id'=>$trx->id]);
        });
    }

    // TAB 2: Entrega DIRECTA (disbursement)
    public function storeDisbursementDirect(Request $req)
    {
        $req->validate([
            'manager_id'             => ['required','exists:users,id'],
            'counterparty_id'        => ['required','exists:users,id'],
            'amount'                 => ['required','numeric','min:0.01'],
            'purpose'                => ['nullable','string','max:255'],
            'performed_at'           => ['nullable','date'],
            'counterparty_signature' => ['required','string'], // firma usuario
            'nip'                    => ['required','digits_between:4,8'],
        ]);

        $me = auth()->user();
        if (!$me || $me->role !== 'admin') {
            abort(403, 'Solo ADMIN puede autorizar una entrega.');
        }
        $this->assertAdminPinOrFail($me, $req->nip);

        // Encargado debe ser ADMIN
        $encargado = User::findOrFail($req->manager_id);
        if ($encargado->role !== 'admin') abort(422, 'El encargado debe tener rol ADMIN.');

        return DB::transaction(function () use ($req, $me) {
            $userSig   = $this->storeDataUrl($req->counterparty_signature, 'signatures');
            $createdAt = $req->performed_at ? Carbon::parse($req->performed_at) : now();

            $trx = CashTransaction::create([
                'type'                        => 'disbursement',
                'manager_id'                  => $req->manager_id,      // admin que entrega
                'counterparty_id'             => $req->counterparty_id, // usuario que recibe
                'amount'                      => $req->amount,
                'purpose'                     => $req->purpose,
                'manager_signature_path'      => null,                  // admin NO firma
                'counterparty_signature_path' => $userSig,              // firma del usuario
                'nip_approved_by'             => $me->id,
                'nip_approved_at'             => now(),
                'created_by'                  => $me->id,
                'created_at'                  => $createdAt,
                'updated_at'                  => $createdAt,
            ]);

            GenerateTransactionReceipt::dispatch($trx->id);
            return response()->json(['ok'=>true, 'id'=>$trx->id]);
        });
    }

    // TAB 2: Entrega con QR (inicia flujo)
    public function startDisbursementWithQr(Request $req)
    {
        $req->validate([
            'manager_id'      => ['required','exists:users,id'],
            'counterparty_id' => ['required','exists:users,id'],
            'amount'          => ['required','numeric','min:0.01'],
            'performed_at'    => ['nullable','date'],
            'nip'             => ['required','digits_between:4,8'],
        ]);

        $me = auth()->user();
        if (!$me || $me->role !== 'admin') {
            abort(403, 'Solo ADMIN puede autorizar una entrega.');
        }
        $this->assertAdminPinOrFail($me, $req->nip);

        $encargado = User::findOrFail($req->manager_id);
        if ($encargado->role !== 'admin') abort(422, 'El encargado debe tener rol ADMIN.');

        return DB::transaction(function () use ($req, $me) {
            $createdAt = $req->performed_at ? Carbon::parse($req->performed_at) : now();
            $token     = Str::uuid()->toString();
            $expires   = now()->addMinutes(20);

            $trx = CashTransaction::create([
                'type'                        => 'disbursement',
                'manager_id'                  => $req->manager_id,
                'counterparty_id'             => $req->counterparty_id,
                'amount'                      => $req->amount,
                'purpose'                     => null, // lo escribirá el usuario desde su cel
                'manager_signature_path'      => null,
                'counterparty_signature_path' => null, // aún no
                'nip_approved_by'             => $me->id,
                'nip_approved_at'             => now(),
                'qr_token'                    => $token,
                'qr_expires_at'               => $expires,
                'created_by'                  => $me->id,
                'created_at'                  => $createdAt,
                'updated_at'                  => $createdAt,
            ]);

            // Generar ruta relativa y convertirla a absoluta con el host deseado
            $relative = route('transactions.qr.show', ['token'=>$token], false);
            $url      = $this->absoluteQr($relative);

            return response()->json(['ok'=>true,'id'=>$trx->id,'token'=>$token,'url'=>$url]);
        });
    }

    // Página pública que ve el usuario al escanear el QR.
    public function showQrForm($token)
    {
        $trx = CashTransaction::where('qr_token',$token)->whereNull('acknowledged_at')->firstOrFail();
        abort_if(now()->greaterThan($trx->qr_expires_at), 410, 'El QR ha expirado.');
        return view('transactions.qr-ack', ['trx'=>$trx]);
    }

    // POST público: usuario escribe motivo y firma (desde su celular).
    public function ackDisbursementWithQr(Request $req, $token)
    {
        $req->validate([
            'purpose'   => ['required','string','max:255'],
            'signature' => ['required','string'],
        ]);

        return DB::transaction(function () use ($req, $token) {
            $trx = CashTransaction::where('qr_token',$token)->whereNull('acknowledged_at')->lockForUpdate()->firstOrFail();
            abort_if(now()->greaterThan($trx->qr_expires_at), 410, 'El QR ha expirado.');

            $userSig = $this->storeDataUrl($req->signature, 'signatures');

            $trx->purpose                     = $req->purpose;
            $trx->counterparty_signature_path = $userSig;
            $trx->acknowledged_at             = now();
            $trx->qr_token                    = null; // invalidar token
            $trx->qr_expires_at               = null;
            $trx->save();

            GenerateTransactionReceipt::dispatch($trx->id);
            return response()->json(['ok'=>true,'id'=>$trx->id]);
        });
    }

    // Polling desde tu UI para saber si el usuario ya firmó.
    public function qrStatus($token)
    {
        $trx = CashTransaction::where('qr_token',$token)->first();
        if (!$trx) {
            $recent = CashTransaction::whereNull('qr_token')
                ->whereNotNull('acknowledged_at')
                ->where('acknowledged_at','>', now()->subMinutes(30))
                ->latest()->first();
            return ['acknowledged'=> (bool)$recent, 'expired'=>false];
        }
        $expired = now()->greaterThan($trx->qr_expires_at);
        return ['acknowledged'=> (bool)$trx->acknowledged_at, 'expired'=>$expired];
    }

    // TAB 3: Devolución (return)
    public function storeReturn(Request $req)
    {
        $req->validate([
            'manager_id'             => ['required','exists:users,id'],
            'counterparty_id'        => ['required','exists:users,id'],
            'amount'                 => ['required','numeric','min:0.01'],
            'purpose'                => ['required','string','max:255'],
            'performed_at'           => ['nullable','date'],
            'manager_signature'      => ['required','string'], // tu firma
            'counterparty_signature' => ['required','string'], // firma del usuario
            'evidence.*'             => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
        ]);

        $encargado = User::findOrFail($req->manager_id);
        if ($encargado->role !== 'admin') abort(422, 'El encargado debe tener rol ADMIN.');

        return DB::transaction(function () use ($req) {
            $mgrSig    = $this->storeDataUrl($req->manager_signature, 'signatures');
            $usrSig    = $this->storeDataUrl($req->counterparty_signature, 'signatures');
            $createdAt = $req->performed_at ? Carbon::parse($req->performed_at) : now();

            $ev = [];
            if ($req->hasFile('evidence')) {
                foreach ($req->file('evidence') as $file) {
                    $ev[] = $file->store('evidence','public');
                }
            }

            $trx = CashTransaction::create([
                'type'                        => 'return',
                'manager_id'                  => $req->manager_id,
                'counterparty_id'             => $req->counterparty_id,
                'amount'                      => $req->amount,
                'purpose'                     => $req->purpose,
                'evidence_paths'              => $ev ?: null,
                'manager_signature_path'      => $mgrSig,
                'counterparty_signature_path' => $usrSig,
                'created_by'                  => auth()->id(),
                'created_at'                  => $createdAt,
                'updated_at'                  => $createdAt,
            ]);

            GenerateTransactionReceipt::dispatch($trx->id);
            return response()->json(['ok'=>true, 'id'=>$trx->id]);
        });
    }

    /**
     * ===================== RECIBO PDF (Dompdf) =====================
     * GET /transactions/{transaction}/receipt
     * Si no existe el archivo, lo genera con Dompdf y luego lo muestra inline.
     * Usa ?download=1 para forzar descarga o ?regen=1 para regenerar.
     */
    public function receipt(Request $request, CashTransaction $transaction)
    {
        // Regenerar si lo piden explícitamente
        if ($request->boolean('regen')) {
            $this->generateAndStorePdf($transaction);
        }

        // Si no hay path o el archivo no está, generamos ahora
        if (!$transaction->pdf_receipt_path || !Storage::disk('public')->exists($transaction->pdf_receipt_path)) {
            $this->generateAndStorePdf($transaction);
        }

        // Si aún no existe algo falló
        if (!$transaction->pdf_receipt_path || !Storage::disk('public')->exists($transaction->pdf_receipt_path)) {
            abort(404, 'Recibo no disponible.');
        }

        $absPath     = Storage::disk('public')->path($transaction->pdf_receipt_path);
        $fileName    = 'recibo-'.$transaction->id.'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response()->file($absPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera el PDF con Dompdf a partir de la vista Blade y lo guarda en storage/public.
     * Devuelve la ruta relativa guardada en el modelo.
     */
    private function generateAndStorePdf(CashTransaction $transaction): string
    {
        // Asegurar relaciones para la vista
        $transaction->loadMissing(['manager:id,name','counterparty:id,name']);

        // Renderizar Blade -> HTML -> PDF
        $pdf = Pdf::loadView('pdfs.transaction', ['trx' => $transaction])
            ->setPaper('a4') // 'letter' si prefieres
            ->setOptions([
                'dpi'               => 96,
                'defaultFont'       => 'DejaVu Sans', // soporta acentos
                'isRemoteEnabled'   => true,          // recomendado
                'isHtml5ParserEnabled' => true,
            ]);

        // Guardar en storage/public/receipts/recibo-XXXX.pdf
        $dir  = 'receipts';
        $path = $dir.'/recibo-'.$transaction->id.'.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        // Persistir en DB
        $transaction->pdf_receipt_path = $path;
        $transaction->save();

        return $path;
    }

    /** ---------------- API "TIEMPO REAL" (dashboard AJAX) ---------------- */

    public function apiChart(Request $r)
    {
        [$from, $to, $fromDT, $toDT] = $this->dateRange($r);

        $rows = $this->baseFilteredQuery($r, $fromDT, $toDT)
            ->selectRaw('DATE(CONVERT_TZ(created_at, "+00:00", @@session.time_zone)) as d, type, SUM(amount) as total')
            ->groupBy('d','type')
            ->orderBy('d')
            ->get();

        $byDay = [];
        foreach ($rows as $row) {
            $byDay[$row->d][$row->type] = (float)$row->total;
        }

        $period    = CarbonPeriod::create($from, $to);
        $chartData = collect($period)->map(function($date) use ($byDay){
            $d = $date->toDateString();
            return [
                'date' => $d,
                'in'   => $byDay[$d]['allocation']   ?? 0.0,
                'out'  => $byDay[$d]['disbursement'] ?? 0.0,
                'ret'  => $byDay[$d]['return']       ?? 0.0,
            ];
        })->values();

        return response()->json($chartData);
    }

    public function apiMetrics(Request $r)
    {
        [$from, $to, $fromDT, $toDT] = $this->dateRange($r);
        $q = $this->baseFilteredQuery($r, $fromDT, $toDT);

        $recibido  = (float) (clone $q)->where('type','allocation')->sum('amount');
        $entregado = (float) (clone $q)->where('type','disbursement')->sum('amount');
        $devuelto  = (float) (clone $q)->where('type','return')->sum('amount');

        $esperadoCaja = $recibido - $entregado + $devuelto;

        $pend = $this->baseFilteredQuery($r, $fromDT, $toDT)
            ->selectRaw("
                counterparty_id,
                SUM(CASE WHEN type='disbursement' THEN amount ELSE 0 END) as entregado,
                SUM(CASE WHEN type='return'       THEN amount ELSE 0 END) as devuelto
            ")
            ->groupBy('counterparty_id')
            ->get()
            ->map(function($row){
                $row->pendiente = (float)$row->entregado - (float)$row->devuelto;
                return $row;
            })
            ->filter(fn($r)=>$r->pendiente > 0.0);

        $lastDisb = $this->baseFilteredQuery($r, $fromDT, $toDT)
            ->where('type','disbursement')
            ->selectRaw('counterparty_id, MAX(created_at) as last_disb')
            ->groupBy('counterparty_id')
            ->pluck('last_disb','counterparty_id');

        $top = $pend->sortByDesc('pendiente')->take(5)->map(function($r) use ($lastDisb){
            $user = User::find($r->counterparty_id);
            $days = isset($lastDisb[$r->counterparty_id]) ? now()->diffInDays($lastDisb[$r->counterparty_id]) : null;
            return [
                'user_id'   => $r->counterparty_id,
                'user_name' => $user?->name ?? ('ID '.$r->counterparty_id),
                'pendiente' => round($r->pendiente, 2),
                'dias'      => $days,
            ];
        })->values();

        $ultimo = (clone $q)->latest()->first(['id','type','amount','created_at','manager_id','counterparty_id']);

        return response()->json([
            'totales' => [
                'recibido'      => round($recibido,2),
                'entregado'     => round($entregado,2),
                'devuelto'      => round($devuelto,2),
                'esperado_caja' => round($esperadoCaja,2),
            ],
            'ranking_pendientes' => $top,
            'ultimo_movimiento'  => $ultimo,
        ]);
    }

   public function apiTransactions(Request $r)
{
    [$from, $to, $fromDT, $toDT] = $this->dateRange($r);
    $q = $this->baseFilteredQuery($r, $fromDT, $toDT)->latest();

    // per_page controlado (10..200)
    $per  = (int) $r->input('per_page', 20);
    $per  = max(10, min($per, 200));

    $page = (int) $r->input('page', 1);
    $p    = $q->paginate($per, ['*'], 'page', $page);

    $items = collect($p->items())->map(function($t){
        return [
            'id'           => $t->id,
            'type'         => $t->type,
            'amount'       => (float)$t->amount,
            'purpose'      => $t->purpose,
            'date'         => $t->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            'manager'      => $t->manager?->name,
            'counterparty' => $t->counterparty?->name,
            'pdf'          => route('transactions.receipt', ['transaction'=>$t->id], false),
            'ack'          => (bool)$t->acknowledged_at,
        ];
    });

    return response()->json([
        'data' => $items,
        'meta' => [
            'page'      => $p->currentPage(),
            'last_page' => $p->lastPage(),
            'total'     => $p->total(),
            'per_page'  => $p->perPage(),
        ]
    ]);
}


    /** ---------------- HELPERS ---------------- */

private function baseFilteredQuery(Request $r, \Carbon\Carbon $fromDT, \Carbon\Carbon $toDT)
{
    return CashTransaction::query()
        ->when($r->filled('manager_id'), fn($qq)=>$qq->where('manager_id', $r->manager_id))
        ->when($r->filled('user_id'),    fn($qq)=>$qq->where('counterparty_id', $r->user_id))
        ->when($r->filled('type'),       fn($qq)=>$qq->where('type', $r->type))
        // IMPORTANTE: filtra en UTC (coherente con cómo se guardan los timestamps)
        ->whereBetween('created_at', [$fromDT, $toDT])
        ->with(['manager:id,name','counterparty:id,name']);
}

private function dateRange(Request $r): array
{
    // Entradas del usuario en TZ de la app
    $appTz = config('app.timezone', 'UTC');

    $from = $r->input('from') ?: now($appTz)->subDays(30)->toDateString();
    $to   = $r->input('to')   ?: now($appTz)->toDateString();

    // Constrúyelos en TZ de la app y conviértelos a UTC para la consulta
    $fromDT = \Carbon\Carbon::parse($from, $appTz)->startOfDay()->utc();
    $toDT   = \Carbon\Carbon::parse($to,   $appTz)->endOfDay()->utc();

    return [$from, $to, $fromDT, $toDT];
}

    private function storeDataUrl(?string $dataUrl, string $folder): ?string
    {
        if (!$dataUrl || !str_contains($dataUrl, ',')) return null;
        [$meta, $content] = explode(',', $dataUrl, 2);
        $ext  = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $path = $folder.'/'.uniqid().".$ext";
        Storage::disk('public')->put($path, base64_decode($content));
        return $path;
    }

    // Construye URL absoluta para el QR con el host fijo
    private function absoluteQr(string $relativePath): string
    {
        return rtrim(self::QR_BASE, '/') . $relativePath;
    }

    /* ---------------- PIN HELPERS (flexible + rehash a bcrypt) ---------------- */

    private function checkPinFlexible(string $plain, ?string $stored): bool
    {
        if (!$stored) return false;

        if (Str::startsWith($stored, ['$2y$', '$2a$', '$2b$'])) {
            return Hash::check($plain, $stored); // bcrypt
        }
        if (Str::startsWith($stored, ['$argon2id$', '$argon2i$'])) {
            return password_verify($plain, $stored); // argon2
        }
        if (config('app.allow_legacy_md5', false) && preg_match('/^[a-f0-9]{32}$/i', $stored)) {
            return hash_equals(strtolower($stored), md5($plain)); // md5 opcional
        }
        return false;
    }

    private function assertAdminPinOrFail(User $user, string $nip): void
    {
        if (!$this->checkPinFlexible($nip, $user->approval_pin_hash ?? null)) {
            abort(422, 'NIP incorrecto.');
        }
        if (!Str::startsWith((string)$user->approval_pin_hash, ['$2y$', '$2a$', '$2b$'])) {
            $user->approval_pin_hash = Hash::make($nip); // migrar a bcrypt
            $user->save();
        } else {
            if (Hash::needsRehash($user->approval_pin_hash)) {
                $user->approval_pin_hash = Hash::make($nip);
                $user->save();
            }
        }
    }
}
