<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashbookEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashbookController extends Controller
{
    public function index(Request $request): View
    {
        $query = CashbookEntry::query()
            ->where('source', 'manual')
            ->whereNull('archived_at')
            ->when($request->filled('direction'), fn ($query) => $query->where('direction', $request->string('direction')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('entry_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('entry_date', '<=', $request->date('to')))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where(fn ($query) => $query->where('reference', 'like', '%'.$request->string('q').'%')->orWhere('note', 'like', '%'.$request->string('q').'%')->orWhere('category', 'like', '%'.$request->string('q').'%')));

        return view('admin.cashbook', [
            'entries' => (clone $query)->latest('entry_date')->paginate(20)->withQueryString(),
            'cashIn' => (clone $query)->where('direction', 'income')->sum('amount'),
            'cashOut' => (clone $query)->where('direction', 'expense')->sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data += ['source' => 'manual', 'user_id' => $request->user()->id];
        $data['reference'] = ($data['reference'] ?? null) ?: 'KAS-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        CashbookEntry::create($data);

        return back()->with('status', 'Transaksi kas disimpan.');
    }

    public function update(Request $request, CashbookEntry $entry): RedirectResponse
    {
        abort_unless($entry->source === 'manual', 403);
        $entry->update($this->validated($request));

        return back()->with('status', 'Transaksi kas diperbarui.');
    }

    public function destroy(CashbookEntry $entry): RedirectResponse
    {
        abort_unless($entry->source === 'manual', 403);
        $entry->update(['archived_at' => now()]);

        return back()->with('status', 'Transaksi kas diarsipkan.');
    }

    public function export(Request $request): StreamedResponse
    {
        $entries = CashbookEntry::query()->where('source', 'manual')->whereNull('archived_at')->orderBy('entry_date')->get();

        return response()->streamDownload(function () use ($entries): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['tanggal', 'tipe', 'kategori', 'metode', 'nominal', 'referensi', 'deskripsi']);
            foreach ($entries as $entry) {
                fputcsv($handle, [$entry->entry_date?->format('Y-m-d'), $entry->direction, $entry->category, $entry->payment_method, $entry->amount, $entry->reference, $entry->note]);
            }
            fclose($handle);
        }, 'buku-kas.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'entry_date' => ['required', 'date'],
            'direction' => ['required', 'in:income,expense'],
            'category' => ['required', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:120'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], ['amount.min' => 'Nominal harus lebih dari nol.']);
    }
}
