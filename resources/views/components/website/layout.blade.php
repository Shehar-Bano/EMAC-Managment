@props(['title' => null])

@include('website.layouts.app', ['title' => $title, 'slot' => $slot])
