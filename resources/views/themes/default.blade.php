{{--
    Theme "default" (OOPS_THEME=default). The tone (`oops.tone`) only picks
    the wording, from resources/lang/{lang}/tones/{tone}.php.

    An open two-column layout (no card): the status's own illustration on a
    soft stage, next to the message and actions. Each status carries a color
    that hints at whose side the problem is on (see
    ErrorPageComposer::STATUS_COLORS); the primary button stays one brand
    blue everywhere. Stacks on small screens.
--}}
<!doctype html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} | {{ $title }}</title>
    <style>
        :root {
            --accent: {{ $colors['accent'] }};
            --soft: {{ $colors['soft'] }};
            --strong: {{ $colors['strong'] }};
            --brand: #1d4ed8;
            --brand-hover: #1e40af;
            --page: #dde5f0;
            --ink: #0b1220;
            --muted: #526079;
            --line: #c9d4e3;
            /* Height of the bottom waves; main keeps this much room free. */
            --waves: clamp(90px, 16vh, 170px);
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            isolation: isolate;
            overflow-x: hidden;
            background-color: var(--page);
            background-image:
                radial-gradient(circle at 22% 42%, color-mix(in srgb, var(--soft) 85%, transparent), transparent 38%),
                linear-gradient(180deg, #e5ecf5 0%, var(--page) 60%);
            color: var(--ink);
            font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ---- Background motif: a fading dot pattern + soft waves ---- */

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(29, 78, 216, 0.16) 1.3px, transparent 1.8px);
            background-size: 24px 24px;
            -webkit-mask-image: radial-gradient(ellipse 60% 55% at 85% 15%, #000, transparent 75%);
            mask-image: radial-gradient(ellipse 60% 55% at 85% 15%, #000, transparent 75%);
        }

        /* Anchored to the end of the page (not the viewport), so long
           content scrolls past the waves instead of sitting on them. */
        .waves {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            width: 100%;
            height: var(--waves);
            pointer-events: none;
        }

        main {
            flex: 1;
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
            padding: 88px 40px calc(var(--waves) + 16px);
            display: grid;
            grid-template-columns: 5fr 6fr;
            align-items: center;
            align-content: center;
            gap: 64px;
        }

        /* ---- Illustration side ---- */

        .art {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1;
            max-width: 420px;
            width: 100%;
            justify-self: center;
        }

        .stage {
            position: relative;
            width: 82%;
            aspect-ratio: 1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 35% 30%, #fff, var(--soft) 72%);
            box-shadow:
                0 0 0 14px color-mix(in srgb, var(--soft) 45%, transparent),
                0 30px 60px -28px color-mix(in srgb, var(--accent) 45%, transparent);
        }

        .illustration {
            width: 64%;
            height: auto;
            overflow: visible;
            animation: float 6s ease-in-out infinite;
        }

        /* ---- Content side ---- */

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 20px;
            padding: 6px 12px 6px 10px;
            border-radius: 999px;
            background: var(--soft);
            border: 1px solid color-mix(in srgb, var(--accent) 22%, transparent);
            color: var(--strong);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .pill::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 18%, transparent);
        }

        h1 {
            margin: 0 0 16px;
            /* Titles are short (errors.php) and always fit on one line. */
            font-size: clamp(26px, 3.1vw, 38px);
            font-weight: 800;
            line-height: 1.15;
            white-space: nowrap;
            letter-spacing: -0.025em;
            /* The status's readable shade: large bold text, WCAG AA on the page. */
            color: var(--strong);
        }

        /* Optional, per tone (e.g. "wise"): a short quote under the title. */
        figure.quote {
            margin: 0 0 18px;
            max-width: 480px;
            padding-left: 16px;
            border-left: 3px solid var(--accent);
        }

        figure.quote blockquote {
            margin: 0;
            font-family: ui-serif, Georgia, Cambria, "Times New Roman", serif;
            font-size: 19px;
            font-style: italic;
            line-height: 1.5;
            color: var(--ink);
        }

        figure.quote figcaption {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        p.message {
            margin: 0 0 32px;
            max-width: 480px;
            font-size: 17px;
            line-height: 1.65;
            color: var(--muted);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 20px;
            border-radius: 12px;
            border: 1px solid transparent;
            font: inherit;
            font-size: 15px;
            font-weight: 650;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .btn:active { transform: translateY(1px); }

        .btn:focus-visible {
            outline: 3px solid color-mix(in srgb, var(--brand) 35%, transparent);
            outline-offset: 2px;
        }

        .btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 10px 24px -10px rgba(29, 78, 216, 0.6);
        }

        .btn-primary:hover { background: var(--brand-hover); }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.7);
            color: var(--ink);
            border-color: var(--line);
        }

        .btn-secondary:hover { background: #fff; border-color: #c3cfe0; }

        p.support {
            margin: 32px 0 0;
            padding-top: 24px;
            max-width: 480px;
            border-top: 1px solid var(--line);
            font-size: 14px;
            line-height: 1.65;
            color: var(--muted);
        }

        p.support code {
            padding: 2px 8px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid var(--line);
            color: var(--ink);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px;
            white-space: nowrap;
            /* One click selects the whole code, ready to copy. */
            user-select: all;
        }

        p.origin {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 14px 0 0;
            max-width: 480px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            color: var(--muted);
        }

        p.origin svg {
            flex: none;
            width: 13px;
            height: 13px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        p.origin span {
            overflow-wrap: anywhere;
        }

        /* Sits on the waves, inside the room main keeps free for them. */
        footer.credit {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 18px;
            text-align: center;
            font-size: 12px;
            letter-spacing: 0.04em;
            /* Darker than --muted: it sits on the tinted waves (WCAG AA). */
            color: #3b4659;
        }

        footer.credit .naiskit-logo {
            width: 16px;
            height: 16px;
            margin-right: 2px;
            vertical-align: -3px;
        }

        footer.credit a {
            color: inherit;
            font-weight: 600;
            text-decoration: none;
        }

        footer.credit a:hover { text-decoration: underline; }

        /* ---- Motion ---- */

        .art { animation: fade 0.7s ease both; }
        .content > * { animation: rise 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .content > :nth-child(2) { animation-delay: 0.06s; }
        .content > :nth-child(3) { animation-delay: 0.12s; }
        .content > :nth-child(4) { animation-delay: 0.18s; }
        .content > :nth-child(5) { animation-delay: 0.24s; }
        .content > :nth-child(6) { animation-delay: 0.3s; }
        .content > :nth-child(7) { animation-delay: 0.36s; }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        @keyframes fade {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: none; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }

        /* ---- Short screens: smaller illustration so content fits ---- */

        @media (max-height: 760px) and (min-width: 821px) {
            .art { max-width: 320px; }

            main { gap: 48px; padding-top: 72px; }
        }

        /* ---- Small screens ---- */

        @media (max-width: 820px) {
            main {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 24px 20px calc(var(--waves) + 8px);
                text-align: center;
            }

            .art { max-width: 240px; }

            p.message, p.support, p.origin { margin-left: auto; margin-right: auto; }

            p.origin { justify-content: center; }

            .actions { justify-content: center; }

            h1 { font-size: min(30px, 6.2vw); }

            figure.quote {
                margin-left: auto;
                margin-right: auto;
                padding: 0;
                border-left: 0;
                max-width: 420px;
            }

            figure.quote::before {
                content: "";
                display: block;
                width: 32px;
                height: 3px;
                margin: 0 auto 12px;
                border-radius: 3px;
                background: var(--accent);
            }
        }
    </style>
</head>
<body>
    <svg class="waves" viewBox="0 0 1440 240" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 120c180-50 360-60 540-24s360 64 540 32 270-62 360-56v168H0z" fill="color-mix(in srgb, var(--accent) 7%, var(--page))"/>
        <path d="M0 170c200-34 400-38 600-8s400 40 600 12c90-12 170-26 240-30v96H0z" fill="color-mix(in srgb, var(--accent) 13%, var(--page))"/>
    </svg>

    <main>
        <div class="art">
            <div class="stage">
                @include('oops::themes.default.illustration')
            </div>
        </div>

        <div class="content">
            <p class="pill">{{ $labels['error_label'] ?? 'Error' }} {{ $code }}</p>
            <h1>{{ $title }}</h1>

            @if (! empty($quote['text']))
                <figure class="quote">
                    <blockquote>“{{ $quote['text'] }}”</blockquote>
                    @if (! empty($quote['author']))
                        <figcaption>{{ $quote['author'] }}</figcaption>
                    @endif
                </figure>
            @endif

            @if (! empty($message))
                <p class="message">{{ $message }}</p>
            @endif

            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/') }}">
                    <svg viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/></svg>
                    {{ $labels['back_home'] ?? '' }}
                </a>
                <button class="btn btn-secondary" type="button" onclick="history.back()">
                    <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    {{ $labels['go_back'] ?? '' }}
                </button>
            </div>

            @if (! empty($support))
                {{-- Escaped first, then only the reference code gets wrapped. --}}
                <p class="support">{!! str_replace(e($reference), '<code>'.e($reference).'</code>', e($support)) !!}</p>
            @endif

            {{-- Where this error came from, useful when one package serves many apps. --}}
            <p class="origin">
                <svg viewBox="0 0 24 24"><path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>
                <span>{{ $url }}</span>
            </p>
        </div>
    </main>

    @if (config('oops.powered_by'))
        <footer class="credit">@include('oops::partials.naiskit-logo') Powered by <a href="https://github.com/naiskit" target="_blank" rel="noopener">Naiskit</a></footer>
    @endif
</body>
</html>
