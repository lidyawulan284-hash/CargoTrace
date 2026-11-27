@props([
    'id',
    'title',
    'maxWidth' => 'max-w-lg',
])

<div id="{{ $id }}" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl {{ $maxWidth }} w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ $title }}</h3>
            <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="text-[#718096] hover:text-[#172033] cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        {{ $slot }}
    </div>
</div>
