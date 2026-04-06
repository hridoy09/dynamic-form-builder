<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dynamic Form' }}</title>
    <style>
        :root {
            --bg: #edf1ef;
            --bg-soft: #f7f8f6;
            --surface: rgba(255, 255, 255, 0.9);
            --surface-strong: #ffffff;
            --line: rgba(38, 57, 54, 0.12);
            --line-strong: rgba(38, 57, 54, 0.2);
            --text: #182423;
            --muted: #60706d;
            --accent: #1f6f64;
            --accent-dark: #15554d;
            --accent-soft: rgba(31, 111, 100, 0.08);
            --highlight: #d6a25e;
            --success-bg: #eaf7f1;
            --success-text: #1d5b43;
            --danger-bg: #fff1ef;
            --danger-text: #9c362d;
            --shadow: 0 24px 60px rgba(22, 40, 37, 0.08);
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 14px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: Aptos, "Segoe UI Variable", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(31, 111, 100, 0.14), transparent 22%),
                radial-gradient(circle at right 20%, rgba(214, 162, 94, 0.18), transparent 18%),
                linear-gradient(180deg, #f8faf9 0%, var(--bg) 54%, #eef3f1 100%);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(24, 36, 35, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(24, 36, 35, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(circle at center, black 35%, transparent 90%);
            pointer-events: none;
            z-index: -1;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            transition: color 160ms ease, opacity 160ms ease;
        }

        a:hover {
            color: var(--accent-dark);
        }

        h1, h2, h3 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
        }

        code {
            padding: 0.16rem 0.42rem;
            border-radius: 999px;
            background: rgba(24, 36, 35, 0.06);
            color: var(--accent-dark);
            font-size: 0.92em;
        }

        .shell {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 28px 0 40px;
        }

        .masthead {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 1.4rem;
            margin-bottom: 1.4rem;
            position: sticky;
            top: 0.75rem;
            z-index: 10;
            backdrop-filter: blur(18px);
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }

        .brand-block {
            display: grid;
            gap: 0.72rem;
            max-width: 760px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            background: rgba(21, 85, 77, 0.08);
            color: var(--accent-dark);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex-wrap: wrap;
        }

        .brand {
            font-size: clamp(1.7rem, 3.4vw, 2.5rem);
            line-height: 1.02;
        }

        .lede {
            color: var(--muted);
            max-width: 68ch;
            line-height: 1.65;
            font-size: 1rem;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            width: fit-content;
            padding: 0.46rem 0.82rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.78);
            color: var(--text);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .pill-success {
            background: linear-gradient(135deg, rgba(31, 111, 100, 0.14), rgba(214, 162, 94, 0.14));
            border-color: rgba(31, 111, 100, 0.18);
            color: var(--accent-dark);
        }

        .pill-outline {
            background: transparent;
        }

        .nav {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .stack { display: grid; gap: 1.1rem; }

        .grid-2, .grid-3, .metric-grid {
            display: grid;
            gap: 1rem;
        }

        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .metric-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }

        .hero {
            display: grid;
            gap: 1rem;
            padding: 1.45rem;
        }

        .hero-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(247,250,249,0.86));
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            padding: 1.35rem;
        }

        .section-title {
            display: grid;
            gap: 0.35rem;
        }

        .section-title p, .hint {
            color: var(--muted);
            line-height: 1.55;
        }

        .metric {
            padding: 1rem 1.1rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255,255,255,0.88), rgba(247,248,246,0.96));
        }

        .metric-value {
            display: block;
            margin-top: 0.42rem;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--accent-dark);
        }

        .metric-label {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            font-weight: 700;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 44px;
            padding: 0.78rem 1.15rem;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(21, 85, 77, 0.18);
            transition: transform 160ms ease, box-shadow 160ms ease, opacity 160ms ease;
        }

        .button:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 16px 34px rgba(21, 85, 77, 0.22);
        }

        .button.secondary,
        .button.ghost,
        .button.flat {
            background: rgba(255, 255, 255, 0.78);
            color: var(--text);
            border: 1px solid var(--line);
            box-shadow: none;
        }

        .button.flat {
            background: rgba(31, 111, 100, 0.08);
            color: var(--accent-dark);
            border-color: rgba(31, 111, 100, 0.12);
        }

        .button.tiny {
            min-height: 34px;
            padding: 0.48rem 0.9rem;
            font-size: 0.86rem;
        }

        .status, .errors {
            padding: 1rem 1.05rem;
            border-radius: 18px;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .status {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: rgba(29, 91, 67, 0.14);
        }

        .errors {
            color: var(--danger-text);
            background: var(--danger-bg);
            border-color: rgba(156, 54, 45, 0.16);
        }

        .errors ul {
            margin: 0.7rem 0 0;
            padding-left: 1.2rem;
        }

        .table-wrap {
            overflow: hidden;
            border-radius: var(--radius-xl);
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.72);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: rgba(24, 36, 35, 0.04);
        }

        .table th,
        .table td {
            padding: 1rem 0.9rem;
            border-bottom: 1px solid rgba(38, 57, 54, 0.08);
            text-align: left;
            vertical-align: top;
        }

        .table th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
        }

        .table tbody tr:hover {
            background: rgba(31, 111, 100, 0.03);
        }

        .surface {
            padding: 1.35rem;
        }

        .surface-soft {
            background: linear-gradient(180deg, rgba(255,255,255,0.76), rgba(245,247,246,0.92));
        }

        .field-card {
            padding: 1.15rem;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(247,249,248,0.92));
            box-shadow: 0 10px 28px rgba(24, 36, 35, 0.05);
        }

        .field-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .field-meta {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .field-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-weight: 800;
        }

        .inline-check,
        .choice-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-height: 46px;
            padding: 0.8rem 0.95rem;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255,255,255,0.78);
        }

        .inline-check input,
        .choice-item input {
            width: auto;
            margin: 0;
            accent-color: var(--accent);
        }

        .choice-list {
            display: grid;
            gap: 0.7rem;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.42rem;
            color: var(--text);
        }

        input, textarea, select {
            width: 100%;
            border: 1px solid rgba(38, 57, 54, 0.14);
            border-radius: 14px;
            padding: 0.88rem 0.95rem;
            background: rgba(255, 255, 255, 0.96);
            color: var(--text);
            font: inherit;
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: rgba(31, 111, 100, 0.46);
            box-shadow: 0 0 0 4px rgba(31, 111, 100, 0.12);
            background: #fff;
        }

        textarea {
            min-height: 128px;
            resize: vertical;
        }

        .split-note {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(220px, 0.8fr);
            gap: 1rem;
        }

        .aside-note {
            padding: 1.05rem;
            border-radius: 18px;
            border: 1px solid rgba(31, 111, 100, 0.12);
            background: linear-gradient(180deg, rgba(31, 111, 100, 0.08), rgba(214, 162, 94, 0.08));
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            width: fit-content;
            border-radius: 999px;
            padding: 0.42rem 0.8rem;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(24, 36, 35, 0.06);
            color: var(--text);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.42rem 0.78rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        .status-pill.active {
            background: rgba(29, 91, 67, 0.12);
            color: #1d5b43;
        }

        .status-pill.draft {
            background: rgba(214, 162, 94, 0.18);
            color: #7b531f;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
        }

        .page-footer-note {
            color: var(--muted);
            font-size: 0.9rem;
        }

        @media (max-width: 900px) {
            .masthead,
            .toolbar,
            .field-toolbar,
            .split-note {
                grid-template-columns: 1fr;
                display: grid;
            }

            .grid-2,
            .grid-3,
            .metric-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .shell {
                width: min(100% - 1rem, 1180px);
                padding-top: 16px;
            }

            .masthead {
                position: static;
            }

            .table-wrap {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="masthead panel">
            <div class="brand-block">
                <span class="eyebrow">Laravel Package UI</span>
                <div class="brand-row">
                    <div class="brand">Dynamic Form Builder</div>
                    <span class="pill pill-success">Production Ready</span>
                </div>
                <p class="lede">Create forms, render them in Blade, collect files, and review submissions from one production-focused workflow.</p>
            </div>
            <div class="nav">
                <a class="button ghost" href="{{ route('dynamic-form.admin.forms.index') }}">Builder</a>
            </div>
        </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
