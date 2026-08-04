<?php

namespace App\Http\Controllers;

use App\Models\Camioneta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CamionetaController extends Controller
{
    public function create()
    {
        return view('camionetas.agregar_camionetas');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCamioneta($request, false);

        try {
            DB::beginTransaction();

            $camioneta = new Camioneta();
            $this->fillCamionetaFields($camioneta, $validated);

            $fotosPaths = [];
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    if ($foto) {
                        $fotosPaths[] = $foto->store('public/fotos');
                    }
                }
            }
            $camioneta->fotos = !empty($fotosPaths) ? json_encode($fotosPaths) : null;

            $this->storePdfIfExists($request, $camioneta, 'tarjeta_circulacion');
            $this->storePdfIfExists($request, $camioneta, 'verificacion');
            $this->storePdfIfExists($request, $camioneta, 'tenencia');
            $this->storePdfIfExists($request, $camioneta, 'seguro');

            $camioneta->save();

            DB::commit();

            return redirect()->route('camionetas.index')->with('success', 'Camioneta registrada con éxito.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error al registrar camioneta', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la camioneta.')
                ->with('error_detalle', $e->getMessage());
        }
    }

    public function index()
    {
        $camionetas = Camioneta::all();
        return view('camionetas.index', compact('camionetas'));
    }

    public function show($id)
    {
        $camioneta = Camioneta::findOrFail($id);
        return view('camionetas.ver_camionetas', compact('camioneta'));
    }

    public function edit($id)
    {
        $camioneta = Camioneta::findOrFail($id);
        return view('camionetas.edit', compact('camioneta'));
    }

    public function update(Request $request, $id)
    {
        $camioneta = Camioneta::findOrFail($id);
        $validated = $this->validateCamioneta($request, true);

        try {
            DB::beginTransaction();

            $this->fillCamionetaFields($camioneta, $validated);

            $this->syncFotos($request, $camioneta);

            $this->replacePdfIfExists($request, $camioneta, 'tarjeta_circulacion');
            $this->replacePdfIfExists($request, $camioneta, 'verificacion');
            $this->replacePdfIfExists($request, $camioneta, 'tenencia');
            $this->replacePdfIfExists($request, $camioneta, 'seguro');

            $camioneta->save();

            DB::commit();

            return redirect()->route('camionetas.index')->with('success', 'Camioneta actualizada con éxito.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error al actualizar camioneta', [
                'camioneta_id' => $id,
                'message'      => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar la camioneta.')
                ->with('error_detalle', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $camioneta = Camioneta::findOrFail($id);

        try {
            DB::beginTransaction();

            foreach ($this->decodeFotos($camioneta->fotos) as $fotoPath) {
                $this->deleteStoredFile($fotoPath);
            }

            $this->deleteStoredFile($camioneta->tarjeta_circulacion);
            $this->deleteStoredFile($camioneta->verificacion);
            $this->deleteStoredFile($camioneta->tenencia);
            $this->deleteStoredFile($camioneta->seguro);

            $camioneta->delete();

            DB::commit();

            return redirect()->route('camionetas.index')->with('success', 'Camioneta eliminada con éxito.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error al eliminar camioneta', [
                'camioneta_id' => $id,
                'message'      => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'No se pudo eliminar la camioneta.')
                ->with('error_detalle', $e->getMessage());
        }
    }

    private function validateCamioneta(Request $request, bool $isUpdate = false): array
    {
        $yearMax = date('Y') + 1;

        return $request->validate([
            'placa'                 => 'required|string|max:255',
            'vin'                   => 'required|string|max:255',
            'marca'                 => 'required|string|max:255',
            'modelo'                => 'required|string|max:255',
            'anio'                  => 'required|integer|min:1900|max:' . $yearMax,
            'color'                 => 'required|string|max:255',
            'tipo_motor'            => 'nullable|string|max:255',
            'capacidad_carga'       => 'nullable|string|max:255',
            'tipo_combustible'      => 'nullable|string|max:255',
            'fecha_adquisicion'     => 'nullable|date',
            'ultimo_mantenimiento'  => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'ultima_verificacion'   => 'nullable|date',
            'proxima_verificacion'  => 'nullable|date',
            'kilometraje'           => 'nullable|integer|min:0',
            'rendimiento_litro'     => 'nullable|numeric|min:0',
            'costo_llenado'         => 'nullable|numeric|min:0',

            'fotos'                 => 'nullable|array',
            'fotos.*'               => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'fotos_eliminadas'      => 'nullable|string',

            'tarjeta_circulacion'   => 'nullable|mimes:pdf|max:5120',
            'verificacion'          => 'nullable|mimes:pdf|max:5120',
            'tenencia'              => 'nullable|mimes:pdf|max:5120',
            'seguro'                => 'nullable|mimes:pdf|max:5120',
        ]);
    }

    private function fillCamionetaFields(Camioneta $camioneta, array $validated): void
    {
        $fields = [
            'placa',
            'vin',
            'marca',
            'modelo',
            'anio',
            'color',
            'tipo_motor',
            'capacidad_carga',
            'tipo_combustible',
            'fecha_adquisicion',
            'ultimo_mantenimiento',
            'proximo_mantenimiento',
            'ultima_verificacion',
            'proxima_verificacion',
            'kilometraje',
            'rendimiento_litro',
            'costo_llenado',
        ];

        foreach ($fields as $field) {
            $camioneta->{$field} = $validated[$field] ?? null;
        }
    }

    private function syncFotos(Request $request, Camioneta $camioneta): void
    {
        $currentPhotos = $this->decodeFotos($camioneta->fotos);

        $deletedPhotos = json_decode($request->input('fotos_eliminadas', '[]'), true);
        if (!is_array($deletedPhotos)) {
            $deletedPhotos = [];
        }

        $deletedPhotos = array_values(array_filter($deletedPhotos, function ($path) {
            return is_string($path) && trim($path) !== '';
        }));

        if (!empty($deletedPhotos)) {
            foreach ($deletedPhotos as $pathToDelete) {
                $this->deleteStoredFile($pathToDelete);
            }

            $currentPhotos = array_values(array_filter($currentPhotos, function ($photoPath) use ($deletedPhotos) {
                return !in_array($photoPath, $deletedPhotos, true);
            }));
        }

        $newFiles = $request->file('fotos', []);
        $newFilesCount = is_array($newFiles) ? count($newFiles) : 0;

        if ((count($currentPhotos) + $newFilesCount) > 4) {
            throw ValidationException::withMessages([
                'fotos' => 'Solo puedes tener hasta 4 imágenes en total, contando las ya guardadas.',
            ]);
        }

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if ($foto) {
                    $currentPhotos[] = $foto->store('public/fotos');
                }
            }
        }

        $camioneta->fotos = !empty($currentPhotos) ? json_encode(array_values($currentPhotos)) : null;
    }

    private function storePdfIfExists(Request $request, Camioneta $camioneta, string $field): void
    {
        if ($request->hasFile($field)) {
            $camioneta->{$field} = $request->file($field)->store('public/documentos');
        }
    }

    private function replacePdfIfExists(Request $request, Camioneta $camioneta, string $field): void
    {
        if ($request->hasFile($field)) {
            $newPath = $request->file($field)->store('public/documentos');
            $oldPath = $camioneta->{$field};

            $camioneta->{$field} = $newPath;

            if ($oldPath && $oldPath !== $newPath) {
                $this->deleteStoredFile($oldPath);
            }
        }
    }

    private function decodeFotos($fotos): array
    {
        if (empty($fotos)) {
            return [];
        }

        if (is_array($fotos)) {
            return array_values(array_filter($fotos));
        }

        $decoded = json_decode($fotos, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        if (is_string($fotos) && trim($fotos) !== '') {
            return [trim($fotos)];
        }

        return [];
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo eliminar archivo del storage', [
                'path'    => $path,
                'message' => $e->getMessage(),
            ]);
        }
    }
}