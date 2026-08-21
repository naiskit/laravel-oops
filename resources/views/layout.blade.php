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
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} — {{ $title }}</title>
    <style>
        @php
            $vars = fn (array $c) => "--bg:{$c['bg']};--card:{$c['card']};--text:{$c['text']};--muted:{$c['muted']};--border:{$c['border']};--accent:{$c['accent']};";
        @endphp

        {{--
            --side-bg/--side-text always derive from the *light* accent,
            regardless of the active mode: --side-bg is a pale tint of it
            (ErrorPageComposer::mixWithWhite), --side-text is the accent at
            full strength on top of that tint. The dark-mode accent is a
            pastel meant for text/borders on a dark page background, not a
            reliable base for this kind of tint — using it would wash out
            differently per status. This keeps the sidebar a consistent,
            soft color-coded panel whether the rest of the card is in light
            or dark mode.
        --}}
        @if ($themeMode === 'dark')
            :root {
                color-scheme: dark;
                {!! $vars($colorsDark) !!}
                --side-bg: {{ $sideBg }};
                --side-text: {{ $sideText }};
            }
        @elseif ($themeMode === 'light')
            :root {
                color-scheme: light;
                {!! $vars($colorsLight) !!}
                --side-bg: {{ $sideBg }};
                --side-text: {{ $sideText }};
            }
        @else
            :root {
                color-scheme: light dark;
                {!! $vars($colorsLight) !!}
                --side-bg: {{ $sideBg }};
                --side-text: {{ $sideText }};
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    {!! $vars($colorsDark) !!}
                }

                {{-- The decorative background is light-mode only; drop it once the OS switches to dark. --}}
                body { background-image: none; }
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
            @if ($bgImage && $themeMode !== 'dark')
                {{-- A faint wash of the status's own accent sits on top of
                     the decorative image, so the color-coding carries onto
                     the page background too, not just the sidebar. --}}
                background-image: linear-gradient({{ $bgOverlay }}, {{ $bgOverlay }}), url('{{ $bgImage }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
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
            /* Layered, softly tinted shadow (rather than flat black) so the
               card reads as floating just above the decorative background
               instead of sitting on it like a flat cutout. */
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
            background: var(--side-bg);
            color: var(--side-text);
            padding: 48px 28px;
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
            width: 84px;
            height: 84px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .icon-badge svg {
            width: 42px;
            height: 42px;
            stroke: var(--side-text);
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

        .mascot {
            display: block;
            height: 160px;
            width: auto;
            max-width: 100%;
            margin-bottom: 12px;
        }

        .code-big {
            font-size: 64px;
            font-weight: 800;
            color: var(--side-text);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .code-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--side-text);
            opacity: 0.75;
            margin-top: 8px;
        }

        .body {
            padding: 40px;
            min-width: 0;
        }

        .title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 12px;
        }

        .title-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            border: 1px solid var(--border);
        }

        .title-icon svg {
            width: 20px;
            height: 20px;
            stroke: var(--accent);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 {
            font-size: 24px;
            line-height: 1.3;
            margin: 0;
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
                align-items: center;
                text-align: center;
                padding: 36px 24px;
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
                @elseif ($mascotImage)
                    <img class="mascot" src="{{ $mascotImage }}" alt="">
                @else
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
                    </div>
                @endif
                <div class="code-big">{{ $code }}</div>
                <div class="code-label">Error</div>
            </div>
            <div class="body">
                <div class="title-row">
                    <div class="title-icon">
                        <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
                    </div>
                    <h1>{{ $title }}</h1>
                </div>

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
