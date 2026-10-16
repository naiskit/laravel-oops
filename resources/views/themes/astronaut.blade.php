{{--
    Theme "astronaut" (OOPS_THEME=astronaut): the original laravel-oops look.
    A card with a color-coded side panel (astronaut mascot + big status
    code) next to the title, tone content and actions, on a soft decorative
    background. Stacks below 560px.

    It keeps its own palette (one accent per status) instead of the default
    theme's STATUS_COLORS. Images come from resources/assets/astronaut/ and
    are inlined, so nothing has to be published.
--}}
@php
    $accents = [
        403 => '#be123c', 404 => '#6d28d9', 419 => '#b45309',
        429 => '#0f766e', 500 => '#dc2626', 503 => '#475569',
    ];
    $icons = [
        403 => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        404 => '<circle cx="12" cy="12" r="9"/><polygon points="12,7 14,12 12,17 10,12" fill="currentColor" stroke="none"/>',
        419 => '<circle cx="12" cy="12" r="9"/><line x1="12" y1="12" x2="12" y2="7"/><line x1="12" y1="12" x2="16" y2="14"/>',
        429 => '<rect x="7" y="6" width="3" height="12" rx="1" fill="currentColor" stroke="none"/><rect x="14" y="6" width="3" height="12" rx="1" fill="currentColor" stroke="none"/>',
        500 => '<circle cx="12" cy="12" r="9"/><line x1="12" y1="7.5" x2="12" y2="13"/><circle cx="12" cy="16.3" r="0.9" fill="currentColor" stroke="none"/>',
        503 => '<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5Z" fill="currentColor" stroke="none"/>',
    ];
    $accent = $accents[$code] ?? '#52525b';
    $icon = $icons[$code] ?? '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/>';
    $mascot = $asset("astronaut/mascot-{$code}.png");
    $background = $asset('astronaut/background.jpg');
@endphp
<!doctype html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} | {{ $title }}</title>
    <style>
        :root {
            --accent: {{ $accent }};
            /* Side panel: a pale tint of the accent, the accent on top. */
            --side-bg: color-mix(in srgb, var(--accent) 16%, #fff);
            --bg: #f5f5f4;
            --card: #ffffff;
            --text: #1c1917;
            --muted: #6b6560;
            --border: #e7e5e4;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-color: var(--bg);
            @if ($background)
                /* A faint wash of the status accent over the decorative image. */
                background-image:
                    linear-gradient(color-mix(in srgb, var(--accent) 4%, transparent), color-mix(in srgb, var(--accent) 4%, transparent)),
                    url('{{ $background }}');
                background-size: cover;
                background-position: center;
            @endif
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 760px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 8px 20px -8px rgba(88, 28, 135, 0.08),
                0 24px 48px -20px rgba(88, 28, 135, 0.10);
        }

        .grid {
            display: grid;
            grid-template-columns: 3fr 5fr;
        }

        .side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 28px;
            background: var(--side-bg);
            color: var(--accent);
            text-align: center;
        }

        .mascot {
            display: block;
            height: 160px;
            width: auto;
            max-width: 100%;
            margin-bottom: 12px;
        }

        .icon-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 84px;
            height: 84px;
            margin-bottom: 24px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .icon-badge svg { width: 42px; height: 42px; }

        svg.glyph {
            stroke: currentColor;
            fill: none;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .code-big {
            font-size: 64px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .code-label {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .body {
            min-width: 0;
            padding: 40px;
        }

        .title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 14px;
        }

        .title-icon {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--accent);
        }

        .title-icon svg { width: 20px; height: 20px; }

        h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.3;
        }

        blockquote {
            margin: 0 0 20px;
            padding: 2px 0 2px 18px;
            border-left: 3px solid var(--accent);
        }

        blockquote p {
            margin: 0 0 6px;
            font-style: italic;
            font-size: 15px;
            line-height: 1.5;
        }

        blockquote cite {
            font-style: normal;
            font-size: 13px;
            color: var(--muted);
        }

        p.message {
            margin: 0 0 24px;
            font-style: italic;
            font-size: 15px;
            line-height: 1.6;
        }

        p.support {
            margin: 0 0 28px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg);
            font-size: 13px;
            line-height: 1.6;
            color: var(--muted);
        }

        p.support code {
            padding: 1px 6px;
            border-radius: 5px;
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            white-space: nowrap;
            user-select: all;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
        }

        .actions a.home {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .actions a.home:hover { filter: brightness(0.92); }

        .actions button.back {
            padding: 0;
            border: 0;
            background: none;
            color: var(--muted);
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .actions button.back:hover { color: var(--accent); }

        .actions a:focus-visible,
        .actions button:focus-visible {
            outline: 3px solid color-mix(in srgb, var(--accent) 35%, transparent);
            outline-offset: 2px;
        }

        /* Origin URL on top, the credit centered under it. */
        .footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            text-align: center;
            margin: 28px 0 0;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--muted);
        }

        .footer .origin {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            overflow-wrap: anywhere;
        }

        .footer .naiskit-logo {
            width: 15px;
            height: 15px;
            margin-right: 2px;
            vertical-align: -3px;
        }

        .footer a {
            color: inherit;
            font-weight: 600;
            text-decoration: none;
        }

        .footer a:hover { color: var(--accent); text-decoration: underline; }

        @media (max-width: 560px) {
            .grid { display: block; }

            .side { padding: 32px 24px; }

            .mascot { height: 130px; }

            .body { padding: 28px 24px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="grid">
            <div class="side">
                @if ($mascot)
                    <img class="mascot" src="{{ $mascot }}" alt="">
                @else
                    <div class="icon-badge"><svg class="glyph" viewBox="0 0 24 24">{!! $icon !!}</svg></div>
                @endif
                <div class="code-big">{{ $code }}</div>
                <div class="code-label">{{ $labels['error_label'] ?? 'Error' }}</div>
            </div>

            <div class="body">
                <div class="title-row">
                    <div class="title-icon"><svg class="glyph" viewBox="0 0 24 24">{!! $icon !!}</svg></div>
                    <h1>{{ $title }}</h1>
                </div>

                @if (! empty($quote['text']))
                    <blockquote>
                        <p>“{{ $quote['text'] }}”</p>
                        @if (! empty($quote['author']))
                            <cite>{{ $quote['author'] }}</cite>
                        @endif
                    </blockquote>
                @endif

                @if (! empty($message))
                    <p class="message">{{ $message }}</p>
                @endif

                @if (! empty($support))
                    {{-- Escaped first, then only the reference code gets wrapped. --}}
                    <p class="support">{!! str_replace(e($reference), '<code>'.e($reference).'</code>', e($support)) !!}</p>
                @endif

                <div class="actions">
                    <a class="home" href="{{ url('/') }}">{{ $labels['back_home'] ?? '' }}</a>
                    <button class="back" type="button" onclick="history.back()">{{ $labels['go_back'] ?? '' }}</button>
                </div>

                <div class="footer">
                    <span class="origin">{{ $url }}</span>
                    @if (config('oops.powered_by'))
                        <span class="credit">@include('oops::partials.naiskit-logo') Powered by <a href="https://github.com/naiskit" target="_blank" rel="noopener">Naiskit</a></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
