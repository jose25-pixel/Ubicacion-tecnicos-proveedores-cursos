@php
    $variant = $variant ?? 'icon';
    $iconPublicPath = public_path('images/brand/zeta-icon.png');
    $fullPublicPath = public_path('images/brand/zeta-full.png');
    $hasIcon = file_exists($iconPublicPath);
    $hasFull = file_exists($fullPublicPath);
@endphp

@if ($variant === 'full' && $hasFull)
    <img src="{{ asset('images/brand/zeta-full.png') }}" alt="Refrigeracion Zeta" {{ $attributes }}>
@elseif ($variant === 'icon' && $hasIcon)
    <img src="{{ asset('images/brand/zeta-icon.png') }}" alt="Refrigeracion Zeta" {{ $attributes }}>
@else
    <svg viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" fill="none" {{ $attributes }}>
        <rect x="4" y="4" width="88" height="88" rx="22" stroke="currentColor" stroke-width="6" />
        <path d="M24 28H72L38 68H72" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@endif
