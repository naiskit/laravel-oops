{{--
    Shared shell used by every per-status view (404.blade.php, 403.blade.php,
    etc.) via @include('oops::layout', [...]). Each status view supplies its
    own $icon (raw inner <svg> markup) — everything else (colors, theme
    mode, logo) comes from ErrorPageComposer::resolveTheme(), driven by
    config('oops.theme'). This file only owns the page chrome (head,
    styles, card structure) so a style tweak doesn't need to be repeated
    in every status file.

    Layout: a left sidebar (icon/logo + a large status code) visually
    separates "this is an error page" from the description/quote/actions
    in the body column, so the two don't blend into one continuous block.
    Stacks to a single column below 560px.
--}}
<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} — {{ $title }}</title>
    <style>
        @php
            $vars = fn (array $c) => "--bg:{$c['bg']};--card:{$c['card']};--text:{$c['text']};--muted:{$c['muted']};--border:{$c['border']};--accent:{$c['accent']};";
        @endphp

        @if ($themeMode === 'dark')
            :root {
                color-scheme: dark;
                {!! $vars($colorsDark) !!}
            }
        @elseif ($themeMode === 'light')
            :root {
                color-scheme: light;
                {!! $vars($colorsLight) !!}
            }
        @else
            :root {
                color-scheme: light dark;
                {!! $vars($colorsLight) !!}
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    {!! $vars($colorsDark) !!}
                }
            }
        @endif

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 680px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .grid {
            display: grid;
            grid-template-columns: 190px 1fr;
        }

        .side {
            background: var(--bg);
            border-right: 1px solid var(--border);
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
        }

        .side.align-center {
            align-items: center;
            text-align: center;
        }

        .side.align-left {
            align-items: flex-start;
            text-align: left;
        }

        .icon-badge {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--card);
            border: 1px solid var(--border);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .icon-badge svg {
            width: 38px;
            height: 38px;
            stroke: var(--accent);
            fill: none;
            stroke-width: 1.6;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .logo-badge {
            display: block;
            max-width: 100%;
            height: auto;
            margin-bottom: 16px;
        }

        .code-big {
            font-size: 40px;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .code-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: 6px;
        }

        .body {
            padding: 40px;
            min-width: 0;
        }

        h1 {
            font-size: 24px;
            line-height: 1.3;
            margin: 0 0 12px;
        }

        p.insight {
            font-style: italic;
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 32px;
        }

        blockquote {
            margin: 0 0 32px;
            padding: 2px 0 2px 20px;
            border-left: 3px solid var(--accent);
        }

        blockquote p.quote-text {
            margin: 0 0 8px;
            font-style: italic;
            font-size: 15px;
            line-height: 1.5;
        }

        blockquote cite {
            display: block;
            font-style: normal;
            font-size: 13px;
            color: var(--muted);
        }

        blockquote p.meaning {
            margin: 10px 0 0;
            padding-top: 10px;
            border-top: 1px dashed var(--border);
            font-style: normal;
            font-size: 13px;
            line-height: 1.5;
            color: var(--muted);
        }

        p.support {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            margin: 0 0 32px;
            font-size: 13px;
            line-height: 1.6;
            color: var(--muted);
        }

        .actions a {
            display: inline-block;
            padding: 10px 20px;
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .footer {
            margin: 32px 0 0;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--muted);
        }

        .footer a {
            color: var(--muted);
            text-decoration: none;
        }

        .footer a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        @media (max-width: 560px) {
            .grid {
                display: block;
            }

            .side {
                border-right: none;
                border-bottom: 1px solid var(--border);
                align-items: center;
                text-align: center;
                padding: 32px 24px;
            }

            .body {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="grid">
            <div class="side {{ $iconAlign === 'center' ? 'align-center' : 'align-left' }}">
                @if ($logoUrl)
                    <img class="logo-badge" src="{{ $logoUrl }}" width="{{ $logoWidth }}" height="{{ $logoHeight }}" alt="">
                @else
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
                    </div>
                @endif
                <div class="code-big">{{ $code }}</div>
                <div class="code-label">Error</div>
            </div>
            <div class="body">
                <h1>{{ $title }}</h1>

                @if (! empty($insight))
                    <p class="insight">{{ $insight }}</p>
                @endif

                @if (! empty($quote))
                    <blockquote>
                        <p class="quote-text">&ldquo;{{ $quote['text'] }}&rdquo;</p>
                        <cite>&mdash; {{ $quote['author'] ?? $unknownAuthorLabel }}@if (! empty($quote['source'])), {{ $quote['source'] }}@endif</cite>
                        @if (! empty($quote['meaning']))
                            <p class="meaning">{{ $quote['meaning'] }}</p>
                        @endif
                    </blockquote>
                @endif

                @if (! empty($support))
                    <p class="support">{{ $support }}</p>
                @endif

                <div class="actions">
                    <a href="{{ url('/') }}">{{ $backHomeLabel }}</a>
                </div>

                @if ($showFooter)
                    <p class="footer">
                        <a href="https://github.com/naiskit/laravel-oops" target="_blank" rel="noopener">Powered by Naiskit</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
