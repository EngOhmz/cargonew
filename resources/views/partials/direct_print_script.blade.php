{{-- Print bar + thermal width only when printing. Screen keeps original readable layout. --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    @media screen {
        body.receipt-direct-print {
            background: #fff;
            padding: 8px 12px 100px 12px;
            margin: 0;
        }
        body.receipt-direct-print img {
            max-width: 100%;
            height: auto;
        }
    }

    @media print {
        @page {
            size: 80mm auto;
            margin: 3mm;
        }
        html, body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: auto;
        }
        body.receipt-direct-print {
            padding: 0 !important;
        }
        .no-print {
            display: none !important;
        }
    }

    .direct-print-hint {
        position: fixed;
        left: 12px;
        right: 12px;
        top: 12px;
        z-index: 9998;
        text-align: center;
        padding: 10px 12px;
        border-radius: 8px;
        background: #fef3c7;
        color: #92400e;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
    }

    .direct-print-bar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: flex;
        gap: 8px;
        justify-content: center;
        padding: 14px 16px;
        padding-bottom: max(14px, env(safe-area-inset-bottom));
        background: rgba(17, 24, 39, 0.95);
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.2);
    }

    .direct-print-bar button {
        border: 0;
        border-radius: 10px;
        padding: 14px 22px;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        min-width: 140px;
        -webkit-tap-highlight-color: transparent;
    }

    .direct-print-bar .btn-print {
        background: #2563eb;
        color: #fff;
        flex: 1;
        max-width: 280px;
    }

    .direct-print-bar .btn-print.pulse {
        animation: printPulse 1.2s ease-in-out infinite;
    }

    .direct-print-bar .btn-close {
        background: #e5e7eb;
        color: #111827;
    }

    @keyframes printPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5); }
        50% { transform: scale(1.03); box-shadow: 0 0 0 8px rgba(37, 99, 235, 0); }
    }
</style>
<script>
(function () {
    document.body.classList.add('receipt-direct-print');

    if (!document.querySelector('meta[name="viewport"]')) {
        var vp = document.createElement('meta');
        vp.name = 'viewport';
        vp.content = 'width=device-width, initial-scale=1';
        document.head.appendChild(vp);
    }

    var printed = false;

    function triggerPrint() {
        try {
            window.focus();
            window.print();
            printed = true;
            hideHint();
            var btn = document.getElementById('direct-print-btn');
            if (btn) btn.classList.remove('pulse');
        } catch (e) {}
    }

    function hideHint() {
        var hint = document.getElementById('direct-print-hint');
        if (hint) hint.style.display = 'none';
    }

    function showHint() {
        var hint = document.getElementById('direct-print-hint');
        if (hint) hint.style.display = 'block';
        var btn = document.getElementById('direct-print-btn');
        if (btn) btn.classList.add('pulse');
    }

    function closePrintWindow() {
        try { window.close(); } catch (e) {}
        setTimeout(function () {
            if (window.closed) return;
            if (window.history.length > 1) {
                try { history.back(); return; } catch (e2) {}
            }
            if (document.referrer) {
                window.location.href = document.referrer;
                return;
            }
            window.location.href = '/staffs/dashboard';
        }, 200);
    }

    function ensurePrintBar() {
        if (!document.getElementById('direct-print-hint')) {
            var hint = document.createElement('div');
            hint.id = 'direct-print-hint';
            hint.className = 'direct-print-hint no-print';
            hint.style.display = 'none';
            hint.textContent = 'Gusa kitufe cha Chapisha / Print hapa chini';
            document.body.appendChild(hint);
        }
        if (document.getElementById('direct-print-bar')) return;
        var bar = document.createElement('div');
        bar.id = 'direct-print-bar';
        bar.className = 'direct-print-bar no-print';
        bar.innerHTML =
            '<button type="button" class="btn-print" id="direct-print-btn">Chapisha / Print</button>' +
            '<button type="button" class="btn-close" id="direct-close-btn">Funga</button>';
        document.body.appendChild(bar);
        document.getElementById('direct-print-btn').addEventListener('click', function (e) {
            e.preventDefault();
            triggerPrint();
        });
        document.getElementById('direct-close-btn').addEventListener('click', closePrintWindow);
    }

    function boot() {
        ensurePrintBar();
        setTimeout(triggerPrint, 400);
        setTimeout(function () {
            if (!printed) showHint();
        }, 1200);
    }

    if (document.readyState === 'complete') {
        boot();
    } else {
        window.addEventListener('load', boot);
    }

    window.addEventListener('pageshow', function (ev) {
        if (ev.persisted) {
            setTimeout(triggerPrint, 300);
        }
    });

    window.addEventListener('afterprint', function () {
        printed = true;
        hideHint();
    });
})();
</script>
