<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $filename ?? 'Print' }}</title>
    <style>
        html, body {
            margin: 0;
            height: 100%;
            background: #525659;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }
        #toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 20;
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            padding: 10px 12px;
            background: rgba(17, 24, 39, 0.95);
            color: #fff;
        }
        #toolbar button {
            border: 0;
            border-radius: 8px;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        #printBtn { background: #2563eb; color: #fff; }
        #closeBtn { background: #e5e7eb; color: #111827; }
        #viewerWrap {
            position: absolute;
            top: 56px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #fff;
        }
        #viewer, #embedViewer {
            border: 0;
            width: 100%;
            height: 100%;
            display: block;
        }
        #fallback {
            display: none;
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: 12px;
            z-index: 25;
            padding: 12px;
            background: #fff;
            border-radius: 10px;
            color: #111827;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,.25);
        }
        @media print {
            #toolbar, #fallback { display: none !important; }
            html, body { background: #fff; height: auto; }
            #viewerWrap {
                position: static !important;
                top: 0 !important;
                height: 100vh !important;
            }
        }
    </style>
</head>
<body>
    <div id="toolbar">
        <button type="button" id="printBtn">Chapisha / Print</button>
        <button type="button" id="closeBtn">Funga</button>
    </div>
    <div id="viewerWrap">
        <iframe id="viewer" title="{{ $filename ?? 'Document' }}"></iframe>
    </div>
    <div id="fallback">
        <p style="margin:0 0 8px;">Bofya <strong>Chapisha / Print</strong> au <strong>Fungua risiti</strong> ikiwa haionekani.</p>
        <p style="margin:0;"><a id="openPdfLink" href="#" target="_blank" rel="noopener">Fungua risiti</a></p>
    </div>
    <script>
    (function () {
        var b64 = @json($pdfBase64);
        var returnUrl = @json($returnUrl ?? url()->previous() ?: url('/staffs/dashboard'));
        var bytes = Uint8Array.from(atob(b64), function (c) { return c.charCodeAt(0); });
        var blob = new Blob([bytes], { type: 'application/pdf' });
        var url = URL.createObjectURL(blob);

        var ua = navigator.userAgent || '';
        var isMobile = /iPhone|iPod|Android.*Mobile|webOS|BlackBerry|IEMobile|Opera Mini|iPad/i.test(ua)
            || ((navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1));

        // Mobile: open PDF in the native viewer (iframe+blob shows white / blob id)
        if (isMobile) {
            window.location.replace(url);
            return;
        }

        var frame = document.getElementById('viewer');
        var openLink = document.getElementById('openPdfLink');
        openLink.href = url;
        openLink.removeAttribute('download');
        frame.src = url;

        // Prefer <embed> as a second desktop path if iframe stays blank
        setTimeout(function () {
            try {
                var blank = !frame.contentDocument || frame.contentDocument.body === null
                    || (frame.contentDocument.body && frame.contentDocument.body.innerHTML === '');
                // cross-origin blob often blocks contentDocument - skip embed swap then
            } catch (e) {}
        }, 2500);

        function tryPrint() {
            try {
                if (frame.contentWindow) {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                    return;
                }
            } catch (e) {}
            try { window.print(); } catch (e2) {}
        }

        function closePrintWindow() {
            try { window.close(); } catch (e) {}
            setTimeout(function () {
                if (window.closed) return;
                if (window.history.length > 1) {
                    try { history.back(); return; } catch (e) {}
                }
                if (document.referrer) {
                    window.location.href = document.referrer;
                    return;
                }
                window.location.href = returnUrl || '/staffs/dashboard';
            }, 200);
        }

        document.getElementById('printBtn').addEventListener('click', tryPrint);
        document.getElementById('closeBtn').addEventListener('click', closePrintWindow);

        frame.addEventListener('load', function () {
            setTimeout(tryPrint, 700);
        });

        setTimeout(function () {
            document.getElementById('fallback').style.display = 'block';
        }, 1800);
    })();
    </script>
</body>
</html>
