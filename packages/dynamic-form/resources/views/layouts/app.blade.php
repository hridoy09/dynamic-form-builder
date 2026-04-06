<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dynamic Form' }}</title>
    <style>
        :root {
            --bg: #f6f1e8;
            --card: #fffdf9;
            --line: #d9c9ae;
            --text: #2c251b;
            --muted: #6a6257;
            --accent: #af5c38;
            --accent-dark: #8d4728;
            --success-bg: #e9f7ee;
            --success-text: #28573a;
            --danger: #a53333;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, rgba(175, 92, 56, 0.12), transparent 30%),
                linear-gradient(180deg, #f8f4ec 0%, var(--bg) 100%);
            color: var(--text);
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: var(--accent-dark); }
        .shell {
            width: min(1120px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 2rem 0 3rem;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            margin-bottom: 2rem;
        }
        .brand {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .nav {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 18px 50px rgba(44, 37, 27, 0.06);
        }
        .stack { display: grid; gap: 1rem; }
        .grid-2 {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .grid-3 {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }
        input, textarea, select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 0.8rem 0.9rem;
            background: #fff;
            color: var(--text);
            font: inherit;
        }
        textarea { min-height: 120px; resize: vertical; }
        .hint {
            color: var(--muted);
            font-size: 0.9rem;
            margin-top: 0.35rem;
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
            border: 0;
            border-radius: 999px;
            padding: 0.8rem 1.2rem;
            background: var(--accent);
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .button.secondary {
            background: #efe2d1;
            color: var(--text);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            background: #efe2d1;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
            color: var(--text);
        }
        .status {
            padding: 0.9rem 1rem;
            border-radius: 14px;
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid rgba(40, 87, 58, 0.15);
            margin-bottom: 1rem;
        }
        .errors {
            padding: 0.9rem 1rem;
            border-radius: 14px;
            color: var(--danger);
            background: #fdeeee;
            border: 1px solid rgba(165, 51, 51, 0.18);
            margin-bottom: 1rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid rgba(217, 201, 174, 0.65);
            text-align: left;
            vertical-align: top;
        }
        .field-card {
            padding: 1rem;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
        }
        .inline-check {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .inline-check input {
            width: auto;
            margin: 0;
        }
        @media (max-width: 800px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="topbar">
            <div>
                <div class="brand">Dynamic Form Builder</div>
                <div class="hint">Create forms, render them in Blade, collect files, and browse submissions.</div>
            </div>
            <div class="nav">
                <a href="{{ route('dynamic-form.admin.forms.index') }}">Builder</a>
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
