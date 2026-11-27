@props(['size' => 'md'])

@php
    $currentLocale = app()->getLocale();
    $isCompact = $size === 'sm';
@endphp

<form method="POST" action="{{ route('locale.switch') }}" {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    @csrf
    <div class="inline-flex items-center gap-0.5 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] p-0.5" role="group" aria-label="{{ __('ui.header.switch_language') }}">
        <span class="pl-2 pr-1 text-[#718096] hidden sm:inline-flex items-center" aria-hidden="true">
            <i data-lucide="globe" class="w-3.5 h-3.5"></i>
        </span>
        <button
            type="submit"
            name="locale"
            value="en"
            title="English"
            aria-label="Switch language to English"
            aria-pressed="{{ $currentLocale === 'en' ? 'true' : 'false' }}"
            class="{{ $isCompact ? 'px-2 py-1 text-[10px]' : 'px-2.5 py-1 text-[11px]' }} rounded-lg font-bold tracking-wide transition cursor-pointer {{ $currentLocale === 'en' ? 'bg-[#F97316] text-white shadow-xs' : 'text-[#718096] hover:text-[#172033] hover:bg-white' }}"
        >
            EN
        </button>
        <button
            type="submit"
            name="locale"
            value="id"
            title="Bahasa Indonesia"
            aria-label="Ubah bahasa ke Bahasa Indonesia"
            aria-pressed="{{ $currentLocale === 'id' ? 'true' : 'false' }}"
            class="{{ $isCompact ? 'px-2 py-1 text-[10px]' : 'px-2.5 py-1 text-[11px]' }} rounded-lg font-bold tracking-wide transition cursor-pointer {{ $currentLocale === 'id' ? 'bg-[#F97316] text-white shadow-xs' : 'text-[#718096] hover:text-[#172033] hover:bg-white' }}"
        >
            ID
        </button>
    </div>
</form>
