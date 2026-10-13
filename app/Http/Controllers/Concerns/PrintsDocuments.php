<?php

namespace App\Http\Controllers\Concerns;

trait PrintsDocuments
{
    /**
     * Stream a PDF through an HTML print bridge (no forced download).
     * Desktop only — mobile POS browsers treat PDF as download.
     */
    protected function streamPdfInline($pdf, string $filename)
    {
        $binary = method_exists($pdf, 'output') ? $pdf->output() : (string) $pdf;

        return $this->streamPdfBinaryInline($binary, $filename);
    }

    /**
     * Same as streamPdfInline but for raw PDF binary (e.g. cached output).
     */
    protected function streamPdfBinaryInline($binary, string $filename)
    {
        // Never send raw PDF to mobile/POS — it triggers "Download" instead of print.
        if ($this->isMobilePrintClient()) {
            return response($binary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => (string) strlen($binary),
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()
            ->view('partials.pdf_browser_print', [
                'filename' => $filename,
                'pdfBase64' => base64_encode($binary),
                'allowClose' => true,
                'returnUrl' => url()->previous() ?: url('/staffs/dashboard'),
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Thermal / POS receipts: always HTML + window.print().
     * PDF on POS/mobile often becomes download or a blank blob page.
     *
     * @param  callable(): \Barryvdh\DomPDF\PDF  $pdfFactory
     */
    protected function printReceiptOrPdf(
        string $view,
        array $htmlData,
        callable $pdfFactory,
        string $filename
    ) {
        // Force HTML for receipts unless explicitly requesting PDF (?pdf=1) on desktop.
        $wantPdf = request()->boolean('pdf') && !$this->isMobilePrintClient();

        if (!$wantPdf) {
            return $this->renderDirectPrint($view, $htmlData);
        }

        return $this->streamPdfInline($pdfFactory(), $filename);
    }

    protected function isMobilePrintClient(): bool
    {
        $ua = (string) (request()->userAgent() ?? '');

        if ($ua === '') {
            // Unknown client (some POS proxies strip UA) — prefer HTML receipt path.
            return true;
        }

        // Phones, tablets, Android POS (Sunmi/iMin/Newland often omit "Mobile")
        if (preg_match(
            '/Mobile|Android|iPhone|iPod|iPad|webOS|BlackBerry|IEMobile|Opera Mini|Silk|Kindle|Sunmi|iMin|Newland|PAYDROID|Wizar|Landi|Pos|POS|Tablet|Touch/i',
            $ua
        )) {
            return true;
        }

        // iPadOS desktop UA spoof
        if (preg_match('/Macintosh/i', $ua) && preg_match('/Mobile/i', $ua)) {
            return true;
        }

        // Explicit override from print links
        if (request()->boolean('html') || request()->boolean('pos')) {
            return true;
        }

        return false;
    }

    /**
     * Render an HTML document and auto-open the browser print dialog.
     * Best for thermal receipts / POS / mobile — no PDF download.
     */
    protected function renderDirectPrint(string $view, array $data = [])
    {
        $data['directPrint'] = true;

        return response()
            ->view($view, $data)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
