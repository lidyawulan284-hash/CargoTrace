@props([
    'label',
    'value',
    'icon' => 'activity',
    'tone' => 'orange',
    'badge' => null,
])

@php
    $tones = [
        'orange' => 'bg-[#FFF7ED] text-[#F97316]',
        'blue' => 'bg-blue-50 text-[#3B82F6]',
        'green' => 'bg-emerald-50 text-[#10A37F]',
        'amber' => 'bg-amber-50 text-[#F59E0B]',
        'red' => 'bg-red-50 text-[#F04452]',
        'navy' => 'bg-[#172033]/5 text-[#172033]',
    ];
    $iconClass = $tones[$tone] ?? $tones['orange'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center justify-between gap-3']) }}>
    <div class="flex items-center gap-3.5 min-w-0">
        <div class="w-10 h-10 rounded-xl {{ $iconClass }} flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
        <div class="min-w-0">
            <div class="text-[11px] font-medium text-[#718096] truncate">{{ $label }}</div>
            <div class="text-xl font-extrabold text-[#172033] tracking-tight">{{ $value }}</div>
        </div>
    </div>
    @if($badge)
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $iconClass }} shrink-0">{{ $badge }}</span>
    @endif
</div>
