@props([
    'icon' => 'inbox',
    'message',
])

<div {{ $attributes->merge(['class' => 'py-12 text-center flex flex-col items-center justify-center gap-2 text-[#718096]']) }}>
    <i data-lucide="{{ $icon }}" class="w-8 h-8 text-[#CBD5E1]"></i>
    <span class="text-xs font-medium">{{ $message }}</span>
</div>
