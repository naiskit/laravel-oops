{{-- 500 — server error / fail and try again. Alert motif. Accent color resolved by ErrorPageComposer. --}}
@include('oops::layout', [
    'icon' => '<circle cx="12" cy="12" r="9"/><line x1="12" y1="7.5" x2="12" y2="13"/><circle cx="12" cy="16.3" r="0.9" fill="currentColor" stroke="none"/>',
])
