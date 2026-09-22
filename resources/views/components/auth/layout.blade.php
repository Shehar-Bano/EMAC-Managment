@props(['title' => null])

@include('auth.layouts.auth', ['title' => $title, 'slot' => $slot])
