@props([
    'title' => null,
    'header' => null,
    'breadcrumbs' => null,
])

@include('dashboard.layouts.app', [
    'title' => $title,
    'header' => $header ?? null,
    'breadcrumbs' => $breadcrumbs ?? null,
    'slot' => $slot,
])
