{{--
    One line illustration per status, drawn on a 160×160 canvas in the
    status's own colors (--accent for lines, --soft/white for fills):
    403 shield + keyhole, 404 magnifier + question mark, 419 hourglass,
    429 speedometer in the red, 500 server + alert, 503 maintenance cone,
    anything else a plain alert.
--}}
<svg class="illustration" viewBox="0 0 160 160" data-status="{{ $code }}" aria-hidden="true" fill="none" stroke="var(--accent)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
    @switch($code)
        @case(403)
            <path d="M80 22l42 15v31c0 30-18 53-42 64-24-11-42-34-42-64V37z" fill="#fff"/>
            <path d="M80 34l30 11v23c0 22-12 39-30 48" stroke="var(--soft)" stroke-width="6"/>
            <circle cx="80" cy="70" r="10" fill="var(--accent)" stroke="none"/>
            <path d="M75 76h10l3 22H72z" fill="var(--accent)" stroke="none"/>
            @break

        @case(404)
            <circle cx="70" cy="70" r="38" fill="#fff"/>
            <circle cx="70" cy="70" r="28" stroke="var(--soft)" stroke-width="6"/>
            <path d="M98 98l30 30" stroke-width="12"/>
            <path d="M61 62a9 9 0 1 1 13 8c-2.6 1.3-4 3-4 5.8V78"/>
            <circle cx="70" cy="89" r="3.2" fill="var(--accent)" stroke="none"/>
            <path d="M130 30v10M125 35h10M28 120v8M24 124h8" stroke-width="3"/>
            @break

        @case(419)
            <path d="M50 26h60M50 134h60" stroke-width="7"/>
            <path d="M58 26c0 28 22 34 22 54s-22 26-22 54M102 26c0 28-22 34-22 54s22 26 22 54" fill="#fff"/>
            <path d="M68 48h24c-3 10-12 15-12 15s-9-5-12-15z" fill="var(--soft)" stroke="none"/>
            <path d="M64 128c4-13 16-18 16-18s12 5 16 18z" fill="var(--accent)" stroke="none"/>
            <path d="M80 86v14" stroke-width="3" stroke-dasharray="1 6"/>
            @break

        @case(429)
            <path d="M28 112a52 52 0 0 1 104 0" stroke="var(--soft)" stroke-width="14"/>
            <path d="M28 112a52 52 0 0 1 104 0" stroke-width="14" pathLength="100" stroke-dasharray="0 72 28 100"/>
            <path d="M80 112l30-34" stroke="#0f172a" stroke-width="5"/>
            <circle cx="80" cy="112" r="9" fill="#0f172a" stroke="none"/>
            <path d="M40 76l6 4M80 56v7M120 76l-6 4" stroke="#94a3b8" stroke-width="3"/>
            <path d="M52 134h56" stroke="var(--soft)" stroke-width="6"/>
            @break

        @case(500)
            <rect x="34" y="30" width="86" height="36" rx="9" fill="#fff"/>
            <rect x="34" y="76" width="86" height="36" rx="9" fill="#fff"/>
            <circle cx="50" cy="48" r="3.5" fill="var(--accent)" stroke="none"/>
            <circle cx="50" cy="94" r="3.5" fill="var(--accent)" stroke="none"/>
            <path d="M64 48h38M64 94h26" stroke="var(--soft)" stroke-width="6"/>
            <circle cx="118" cy="114" r="22" fill="var(--accent)" stroke="#fff" stroke-width="5"/>
            <path d="M118 103v12" stroke="#fff" stroke-width="5"/>
            <circle cx="118" cy="124" r="3" fill="#fff" stroke="none"/>
            @break

        @case(503)
            <path d="M80 24l28 92H52z" fill="#fff"/>
            <path d="M68 62h24M61 88h38" stroke-width="9"/>
            <rect x="34" y="116" width="92" height="14" rx="5" fill="var(--accent)" stroke="none"/>
            <path d="M128 40l-10 10M134 58h-12M28 54h10" stroke="var(--soft)" stroke-width="5"/>
            @break

        @default
            <circle cx="80" cy="80" r="46" fill="#fff"/>
            <circle cx="80" cy="80" r="36" stroke="var(--soft)" stroke-width="6"/>
            <path d="M80 58v28" stroke-width="7"/>
            <circle cx="80" cy="102" r="4.5" fill="var(--accent)" stroke="none"/>
    @endswitch
</svg>
