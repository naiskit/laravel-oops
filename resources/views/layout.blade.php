{{--
    Shared shell used by every per-status view (404.blade.php, 403.blade.php,
    etc.) via @include('oops::layout', [...]). Each status view supplies its
    own $icon (raw inner <svg> markup) — everything else (colors, theme
    mode, logo) comes from ErrorPageComposer::resolveTheme(), driven by
    config('oops.theme'). This file only owns the page chrome (head,
    styles, card structure) so a style tweak doesn't need to be repeated
    in every status file.
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
            max-width: 560px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .icon-badge {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            border: 1px solid var(--border);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .icon-badge svg {
            width: 28px;
            height: 28px;
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
            margin-bottom: 20px;
        }

        .code {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: var(--accent);
            text-transform: uppercase;
            margin: 0 0 8px;
        }

        h1 {
            font-size: 28px;
            line-height: 1.3;
            margin: 0 0 12px;
        }

        p.message {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 32px;
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
    </style>
</head>
<body>
    <div class="card">
        @php
            $badgeStyle = $iconAlign === 'center' ? 'margin-left:auto;margin-right:auto;' : '';
        @endphp
        @if ($logoUrl)
            <img class="logo-badge" src="{{ $logoUrl }}" width="{{ $logoWidth }}" height="{{ $logoHeight }}" alt="" style="{{ $badgeStyle }}">
        @else
            <div class="icon-badge" style="{{ $badgeStyle }}">
                <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
            </div>
        @endif

        <p class="code">Error {{ $code }}</p>
        <h1>{{ $title }}</h1>
        <p class="message">{{ $message }}</p>

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

        <div class="actions">
            <a href="{{ url('/') }}">{{ $backHomeLabel }}</a>
        </div>
    </div>
</body>
</html>
