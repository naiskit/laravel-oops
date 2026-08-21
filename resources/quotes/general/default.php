<?php

// Fallback for any other status code that doesn't have its own group.
//
// Every quote below is checked against a primary source (a book, essay,
// or speech) — exact wording, correct work title, correct attribution,
// no paraphrase, verifiable source. No quote aggregators, no "Anonymous".
// Marie Curie's "Nothing in life is to be feared, it is only to be
// understood" was dropped — even Wikiquote flags its primary source as
// unconfirmed, tracing only to a secondary biography describing it as
// something she "often said to reporters," not a written original.
// Andrea Hirata's dream quote was also dropped — multiple sources quote
// it with different, inconsistent exact wording, so no single version
// could be confirmed as the real novel text.

return [
    [
        'text' => 'It does not do to dwell on dreams and forget to live.',
        'author' => 'J.K. Rowling',
        'source' => 'Harry Potter and the Philosopher\'s Stone (1997)',
        'lang' => 'en',
        'genre' => 'wise',
        'meaning' => 'Dwelling too long on what could be tends to cost what\'s happening right now.',
    ],
    [
        'text' => 'It is only with the heart that one can see rightly; what is essential is invisible to the eye.',
        'author' => 'Antoine de Saint-Exupéry',
        'source' => 'The Little Prince (1943)',
        'lang' => 'en',
        'genre' => 'wise',
        'meaning' => 'Some things only make sense once you stop trying to just look at them.',
    ],
    [
        'text' => 'It is hard to fail, but it is worse never to have tried to succeed.',
        'author' => 'Theodore Roosevelt',
        'source' => '"The Strenuous Life" speech, Hamilton Club, Chicago (1899)',
        'lang' => 'en',
        'genre' => 'formal',
        'meaning' => 'Failing stings less, in the end, than never having tried at all.',
    ],
    [
        'text' => 'Although the world is full of suffering, it is full also of the overcoming of it.',
        'author' => 'Helen Keller',
        'source' => 'The Open Door (1957)',
        'lang' => 'en',
        'genre' => 'wise',
        'meaning' => 'For every bit of trouble here, there\'s usually just as much quiet work already undoing it.',
    ],
    [
        'text' => 'Aku mau hidup seribu tahun lagi.',
        'author' => 'Chairil Anwar',
        'source' => 'puisi "Aku" (1943)',
        'lang' => 'id',
        'genre' => 'wise',
        'meaning' => 'Sekali hidup, rasanya memang ingin terus ada dan terus berkarya.',
    ],
    [
        'text' => 'Well done is better than well said.',
        'author' => 'Benjamin Franklin',
        'source' => 'Poor Richard\'s Almanack (1737)',
        'lang' => 'en',
        'genre' => 'wise',
        'meaning' => 'The doing tends to speak for itself, eventually.',
    ],
];
