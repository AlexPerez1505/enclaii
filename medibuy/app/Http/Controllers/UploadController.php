<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $r)
    {
        $r->validate([
            'file' => ['required', 'file'],
        ]);

        $file = $r->file('file');

        $path = $file->store('evidences/'.date('Y/m/d'), ['disk' => 'public']);

        $att = Attachment::create([
            'attachable_type' => null, // temporal
            'attachable_id'   => null,
            'uploaded_by'     => $r->user()->id,
            'disk'            => 'public',
            'path'            => $path,
            'original_name'   => $file->getClientOriginalName(),
            'mime'            => $file->getMimeType(),
            'size'            => $file->getSize(),
            'sha256'          => hash_file('sha256', $file->getRealPath()),
        ]);

        return response()->json([
            'ok'   => true,
            'id'   => $att->id,
            'name' => $att->original_name,
            'mime' => $att->mime,
            'size' => $att->size,
            'url'  => Storage::disk($att->disk)->url($att->path),
        ]);
    }
}