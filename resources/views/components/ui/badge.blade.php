@props(['tone' => 'neutral'])

@php
    $tones = [
        'success' => 'bg-emerald-50 text-[#10A37F] border-emerald-200',
        'info' => 'bg-blue-50 text-[#3B82F6] border-blue-200',
        'warning' => 'bg-amber-50 text-[#F59E0B] border-amber-200',
        'danger' => 'bg-red-50 text-[#F04452] border-red-200',
        'orange' => 'bg-[#FFF7ED] text-[#F97316] border-[#FDBA74]/60',
        'navy' => 'bg-[#172033] text-white border-[#172033]',
        'neutral' => 'bg-[#F4F5F9] text-[#718096] border-[#E6EAF0]',
    ];
    $cls = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {$cls}"]) }}>
    {{ $slot }}
</span>
