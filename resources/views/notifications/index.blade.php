@extends('layouts.app')

@section('title', 'Notification Center')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notification Center</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time alerts, checkpoint updates, and exception incidents</p>
        </div>
        <form method="POST" action="{{ route('notifications.read_all') }}">
            @csrf
            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                Mark All as Read
            </button>
        </form>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden divide-y divide-slate-100">
        @forelse($notifications as $n)
            <div class="p-4 flex items-start justify-between space-x-4 {{ is_null($n->read_at) ? 'bg-sky-50/30' : '' }} hover:bg-slate-50 transition">
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ is_null($n->read_at) ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'bg-slate-100 text-slate-500' }}">
                        @if($n->type === 'EXCEPTION_OPENED')
                            🚨
                        @elseif($n->type === 'EXCEPTION_RESOLVED')
                            ✅
                        @elseif($n->type === 'STATUS_UPDATE')
                            🚢
                        @else
                            🔔
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 flex items-center gap-2">
                            <span>{{ $n->title }}</span>
                            @if(is_null($n->read_at))
                                <span class="w-2 h-2 rounded-full bg-sky-500 inline-block"></span>
                            @endif
                        </div>
                        <p class="text-slate-600 mt-0.5">{{ $n->message }}</p>
                        <span class="text-[10px] text-slate-400 mt-1 block">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-2 flex-shrink-0">
                    @if($n->link)
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white rounded-lg text-xs font-bold transition">
                                View &rarr;
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-slate-400 text-xs">
                No notifications logged yet.
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
