<?php

return [

    // Turn off to fall back to Laravel's own error pages.
    // Via .env: OOPS_ENABLED=false
    'enabled' => env('OOPS_ENABLED', true),

    // Page design:
    //   "default":   open layout with an illustration per status
    //   "astronaut": card with an astronaut mascot (the original look)
    // Add your own: resources/views/vendor/oops/themes/{name}.blade.php,
    // then OOPS_THEME={name}. An unknown value falls back to "default".
    // Via .env: OOPS_THEME=astronaut
    'theme' => env('OOPS_THEME', 'default'),

    // Wording of the error messages (the page design stays the same):
    //   "formal": polite and soft (default)
    //   "casual": relaxed, everyday
    //   "fun":    playful
    //   "wise":   calm and reflective, opens with a quote
    //   "brief":  one short sentence, no small talk
    //   "technical": what the status means and what to check (for IT/devs)
    // Add your own: lang/vendor/oops/{en,id}/tones/{name}.php, then
    // OOPS_TONE={name}. It only needs the keys it changes; the rest comes
    // from "formal".
    // Via .env: OOPS_TONE=casual
    'tone' => env('OOPS_TONE', 'formal'),

    // Page language: "en" (default) or "id".
    // Via .env: OOPS_LANG=id
    'lang' => env('OOPS_LANG', 'en'),

    // "Powered by Naiskit" in the footer.
    // Via .env: OOPS_POWERED_BY=false to hide it.
    'powered_by' => env('OOPS_POWERED_BY', true),

];
