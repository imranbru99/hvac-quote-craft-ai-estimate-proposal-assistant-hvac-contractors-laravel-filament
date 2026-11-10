<?php

namespace App\Services;

/**
 * Bridge for imrandevbd/laravel-unicode-pdf multilingual documents.
 */
class DocumentPdfService
{
    public function invoicePayload(array $invoice): array
    {
        return [
            'title' => ($invoice['code'] ?? 'INV').' — '.config('app.name'),
            'locale' => app()->getLocale(),
            'author' => config('app.author.name'),
            'lines' => $invoice['lines'] ?? [],
        ];
    }
}
