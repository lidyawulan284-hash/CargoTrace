@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $variants = [
        'primary' => 'bg-[#F97316] hover:bg-[#EA580C] text-white shadow-xs',
        'navy' => 'bg-[#172033] hover:bg-[#1F2B44] text-white shadow-xs',
        'secondary' => 'bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] border border-[#E6EAF0]',
        'danger' => 'bg-[#F04452] hover:bg-red-600 text-white shadow-xs',
        'soft' => 'bg-[#FFF7ED] hover:bg-[#F97316] text-[#F97316] hover:text-white border border-[#FDBA74]/50',
    ];
    $base = 'inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</button>
@endif
