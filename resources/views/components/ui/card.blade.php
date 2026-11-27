@props(['padding' => 'p-5'])

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border border-[#E6EAF0] shadow-saas {$padding}"]) }}>
    {{ $slot }}
</div>
