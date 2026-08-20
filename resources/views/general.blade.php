{{-- Fallback view for any status code without its own file. Neutral dot motif, zinc accent. --}}
@include('oops::layout', [
    'icon' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/>',
    'accentLight' => '#52525b',
    'accentDark' => '#a1a1aa',
])
