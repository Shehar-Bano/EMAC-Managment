@props(['title' => null, 'maxWidth' => null])

@include('auth.layouts.auth', ['title' => $title, 'maxWidth' => $maxWidth, 'slot' => $slot])
