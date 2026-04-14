@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel | HouseHub</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            margin: 0;
            padding: 24px;
            line-height: 1.5;
        }
        .wrap {
            max-width: 720px;
            margin: 0 auto;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 8px;
            color: #f8fafc;
        }
        .sub {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 28px;
        }
        section {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        section h2 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 16px;
            color: #f1f5f9;
        }
        label {
            display: block;
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #f8fafc;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        button[type="button"] {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }
        .btn-primary:hover { background: #2563eb; }
        .btn-muted {
            background: #475569;
            color: #f1f5f9;
        }
        .btn-muted:hover { background: #64748b; }
        .status {
            margin-top: 12px;
            font-size: 0.85rem;
            min-height: 1.25em;
        }
        .status.ok { color: #4ade80; }
        .status.err { color: #f87171; }
        .status.muted { color: #64748b; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Admin Control Panel</h1>
        <p class="sub">Internal tools — session required.</p>

        <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

        <section aria-labelledby="bmm-heading">
            <h2 id="bmm-heading">Broadcast Message Mode</h2>
            <label for="bmm-message">Message</label>
            <textarea id="bmm-message" placeholder="Broadcast message for displays…"></textarea>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button type="button" class="btn-primary" id="bmm-send">Send broadcast</button>
                <button type="button" class="btn-muted" id="clear-omm-btn">Clear</button>
            </div>
            <div class="status muted" id="bmm-status" aria-live="polite"></div>
        </section>

        <section aria-labelledby="em-heading">
            <h2 id="em-heading">Emergency Mode</h2>
            <p class="sub" style="margin-top:-8px;margin-bottom:12px;">Placeholder — no backend yet.</p>
            <label for="em-note">Emergency note</label>
            <textarea id="em-note" placeholder="Reserved for future use…"></textarea>
            <button type="button" class="btn-muted" id="em-btn">Submit (placeholder)</button>
            <div class="status muted" id="em-status" aria-live="polite"></div>
        </section>

        <section aria-labelledby="mp-heading">
            <h2 id="mp-heading">Manual Points</h2>
            <label for="mp-student-id">Student ID</label>
            <input type="number" id="mp-student-id" min="1" step="1" placeholder="e.g. 42">
            <label for="mp-amount">Amount</label>
            <input type="number" id="mp-amount" step="1" placeholder="e.g. 5 or -2">
            <button type="button" class="btn-primary" id="mp-send">Post points</button>
            <div class="status muted" id="mp-status" aria-live="polite"></div>
        </section>
    </div>

    <script>
        (function () {
            const csrf = document.getElementById('csrf-token').value;

            function setStatus(el, text, cls) {
                el.textContent = text || '';
                el.className = 'status ' + (cls || 'muted');
            }

            document.getElementById('bmm-send').addEventListener('click', async function () {
                const message = document.getElementById('bmm-message').value.trim();
                const el = document.getElementById('bmm-status');
                if (!message) {
                    setStatus(el, 'Message is required.', 'err');
                    return;
                }
                setStatus(el, 'Sending…', 'muted');
                try {
                    const res = await fetch('/broadcast-messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ message: message })
                    });
                    const data = await res.json().catch(function () { return {}; });
                    if (!res.ok) {
                        const msg = data.message || (data.errors ? JSON.stringify(data.errors) : 'Request failed (' + res.status + ')');
                        setStatus(el, msg, 'err');
                        return;
                    }
                    setStatus(el, 'Broadcast saved (id ' + (data.id != null ? data.id : '?') + ').', 'ok');
                } catch (e) {
                    setStatus(el, 'Network error.', 'err');
                }
            });

            document.getElementById('clear-omm-btn')?.addEventListener('click', async function () {
                const el = document.getElementById('bmm-status');
                setStatus(el, 'Clearing…', 'muted');
                try {
                    const res = await fetch('/omm/clear', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) {
                        setStatus(el, 'Unable to clear message.', 'err');
                        return;
                    }
                    setStatus(el, 'Message cleared.', 'ok');
                    location.reload();
                } catch (e) {
                    setStatus(el, 'Network error.', 'err');
                }
            });

            document.getElementById('em-btn').addEventListener('click', function () {
                const el = document.getElementById('em-status');
                setStatus(el, 'Emergency Mode is not wired up yet.', 'muted');
            });

            document.getElementById('mp-send').addEventListener('click', async function () {
                const sid = document.getElementById('mp-student-id').value.trim();
                const amountRaw = document.getElementById('mp-amount').value.trim();
                const el = document.getElementById('mp-status');
                if (!sid || amountRaw === '') {
                    setStatus(el, 'Student ID and amount are required.', 'err');
                    return;
                }
                setStatus(el, 'Posting…', 'muted');
                const fd = new FormData();
                fd.append('student_id', sid);
                fd.append('amount', amountRaw);
                try {
                    const res = await fetch('/points', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: fd
                    });
                    const data = await res.json().catch(function () { return {}; });
                    if (!res.ok || data.success === false) {
                        setStatus(el, data.message || 'Request failed (' + res.status + ').', 'err');
                        return;
                    }
                    setStatus(el, 'OK — amount ' + data.amount + (data.student ? ' for ' + data.student : '') + '.', 'ok');
                } catch (e) {
                    setStatus(el, 'Network error.', 'err');
                }
            });
        })();
    </script>
</body>
</html>

@endsection
