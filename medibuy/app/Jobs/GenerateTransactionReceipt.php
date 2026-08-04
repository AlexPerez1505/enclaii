<?php
// app/Jobs/GenerateTransactionReceipt.php
namespace App\Jobs;

use App\Models\CashTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateTransactionReceipt implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  public function __construct(public int $transactionId){}

  public function handle(): void {
    $t = CashTransaction::with(['manager','counterparty','approver'])->findOrFail($this->transactionId);

    $pdf = Pdf::loadView('pdfs.transaction', ['t'=>$t]);
    $path = 'receipts/receipt_'.$t->id.'.pdf';
    Storage::disk('public')->put($path, $pdf->output());

    $t->update(['pdf_receipt_path'=>$path]);

    // Opcional: correo
    // Mail::to($t->counterparty->email)->send(new TransactionReceiptMail($t));
  }
}
