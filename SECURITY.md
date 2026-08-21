# Security Policy

## Supported Versions

Only the latest tagged release of `laravel-oops` receives security fixes.
There is no long-term support for older major versions.

## What This Package Touches

`laravel-oops` renders a Blade view in place of Laravel's default error
page for a configured set of HTTP status codes. It reads `config('oops.*')`
and a list of quotes from local PHP files; it does not accept user input,
make outbound network calls, or store data anywhere. The main risk surface
worth reporting on is:

- The rendered page unintentionally exposing exception details (messages,
  stack traces, file paths) that should stay server-side.
- The published config/quotes/views mechanism being usable to include
  arbitrary files outside the intended directories.

## Reporting a Vulnerability

If you find a security issue, please email **miftahfirdaus.id@gmail.com**
instead of opening a public GitHub issue. Include:

- A description of the issue and its impact.
- Steps to reproduce (a minimal repro repo or code snippet helps a lot).
- The package version and Laravel/PHP versions involved.

We'll acknowledge the report as soon as we can and aim to ship a fix before
any public disclosure. Please give us a reasonable window to do that before
disclosing publicly.
