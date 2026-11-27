@extends('layouts.app')

@section('title', __('ui.notifications.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.notifications.heading') }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.notifications.heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.notifications.subheading') }}</p>
        </div>
        <form method="POST" action="{{ route('notifications.read_all') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white rounded-xl text-xs font-bold transition cursor-pointer">
                <i data-lucide="check-check" class="w-4 h-4"></i>
                <span>{{ __('ui.notifications.mark_all_read') }}</span>
            </button>
        </form>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas overflow-hidden divide-y divide-[#E6EAF0]">
        @forelse($notifications as $n)
            @php
                $isUnread = is_null($n->read_at);
                [$iconName, $iconClass] = match($n->type) {
                    'EXCEPTION_OPENED' => ['alert-triangle', 'bg-red-50 text-[#F04452]'],
                    'EXCEPTION_RESOLVED' => ['check-circle-2', 'bg-emerald-50 text-[#10A37F]'],
                    'STATUS_UPDATE' => ['ship', 'bg-blue-50 text-[#3B82F6]'],
                    default => ['bell', 'bg-[#FFF7ED] text-[#F97316]'],
                };
            @endphp
            <div class="p-4 sm:p-5 flex items-start justify-between gap-4 {{ $isUnread ? 'bg-[#FFF7ED]/50' : '' }} hover:bg-[#F4F5F9]/60 transition">
                <div class="flex items-start gap-3.5 text-xs">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $iconClass }}">
                        <i data-lucide="{{ $iconName }}" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="font-bold text-[#172033] flex items-center gap-2 text-sm">
                            <span>{{ $n->title }}</span>
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-[#F97316] inline-block"></span>
                            @endif
                        </div>
                        <p class="text-[#718096] mt-1 leading-relaxed">{{ $n->message }}</p>
                        <span class="text-[10px] text-[#718096] font-mono mt-1.5 block">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if($n->link)
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white rounded-lg text-xs font-bold transition cursor-pointer">
                            <span>{{ __('ui.common.view') }}</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center py-14 text-[#718096] text-xs flex flex-col items-center gap-2">
                <i data-lucide="bell-off" class="w-8 h-8 text-[#CBD5E1]"></i>
                <span>{{ __('ui.notifications.empty') }}</span>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0]">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
