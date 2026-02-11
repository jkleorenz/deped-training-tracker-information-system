<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print PDS - {{ $userName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #1a1a1a;
        }
        .print-wrapper {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
        }
        .toolbar {
            flex: 0 0 auto;
            padding: 10px 16px;
            background: #1E35FF;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .toolbar h1 {
            font-size: 1rem;
            font-weight: 600;
        }
        .toolbar-actions {
            display: flex;
            gap: 8px;
        }
        .toolbar-actions a,
        .toolbar-actions button {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .toolbar-actions a:hover,
        .toolbar-actions button:hover {
            background: rgba(255,255,255,0.25);
        }
        .toolbar-actions .btn-print {
            background: #fff;
            color: #1E35FF;
            border-color: #fff;
        }
        .toolbar-actions .btn-print:hover {
            background: #f0f0f0;
        }
        #pds-frame {
            flex: 1;
            width: 100%;
            min-height: 0;
            border: none;
            background: #fff;
        }
        .loading {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #1a1a1a;
            color: #e0e0e0;
            font-size: 0.9rem;
        }
        .loading.hidden { display: none; }
        .loading .spinner { width: 32px; height: 32px; border: 3px solid #333; border-top-color: #1E35FF; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media print {
            .toolbar, .loading, .no-print { display: none !important; }
            .print-wrapper { position: static; }
            #pds-frame {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-wrapper">
        <div class="toolbar no-print">
            <h1>Personal Data Sheet (PDS)</h1>
            <div class="toolbar-actions">
                <button type="button" class="btn-print" id="btn-print" title="Open print dialog">Print</button>
            </div>
        </div>
        <div class="loading" id="loading"><div class="spinner"></div><span>Loading PDS…</span><span style="font-size:0.8rem;color:#888;">Print dialog will open automatically.</span></div>
        <iframe
            id="pds-frame"
            src="{{ $pdfUrl }}"
            title="Personal Data Sheet PDF"
        ></iframe>
    </div>
    <script>
        (function () {
            var frame = document.getElementById('pds-frame');
            var loading = document.getElementById('loading');
            var btnPrint = document.getElementById('btn-print');
            var printTriggered = false;

            function triggerPrint() {
                if (printTriggered) return;
                printTriggered = true;
                try { window.focus(); window.print(); } catch (e) {}
            }

            frame.addEventListener('load', function () {
                loading.classList.add('hidden');
                setTimeout(triggerPrint, 400);
            });

            btnPrint.addEventListener('click', function () { triggerPrint(); });

            setTimeout(function () {
                loading.classList.add('hidden');
                if (!printTriggered) setTimeout(triggerPrint, 400);
            }, 10000);
        })();
    </script>
</body>
</html>
