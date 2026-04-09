@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sm-header">
        <div>
            <h4 class="sm-title">{{ __('Messages') }}</h4>
            <p class="sm-subtitle">{{ __('Chat with your instructors and manage your messages here.') }}</p>
        </div>
    </div>

    <div class="sm-grid">
        <div class="sm-list">
            <h6 class="sm-list__title">{{ __('Instructors') }}</h6>
            <div class="sm-contacts">
                @forelse ($instructors as $teacher)
                    @php
                        $active = $partner && $partner->id === $teacher->id;
                        $lastMessage = $threads[$teacher->id] ?? null;
                        $unread = $unreadCounts[$teacher->id] ?? 0;
                    @endphp
                    <a href="{{ route('student.messages.index', $teacher->id) }}" class="sm-contact {{ $active ? 'is-active' : '' }}">
                        <div class="sm-contact__avatar">
                            <img src="{{ asset($teacher->image ?? 'frontend/img/placeholder/instructor.png') }}" alt="{{ \Illuminate\Support\Str::before($teacher->name, ' ') ?: $teacher->name }}">
                        </div>
                        <div class="sm-contact__meta">
                            <div class="sm-contact__name">{{ \Illuminate\Support\Str::before($teacher->name, ' ') ?: $teacher->name }}</div>
                            <div class="sm-contact__last">
                                @if($lastMessage)
                                    {{ \Illuminate\Support\Str::limit($lastMessage->body, 36) }}
                                @else
                                    {{ __('No messages yet.') }}
                                @endif
                            </div>
                        </div>
                        @if($unread > 0)
                            <span class="sm-badge">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <p class="text-muted">{{ __('No instructors found yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="sm-thread">
            @if ($partner)
                <div class="sm-thread__header">
                    <div class="sm-thread__user">
                        <div class="sm-contact__avatar">
                            <img src="{{ asset($partner->image ?? 'frontend/img/placeholder/instructor.png') }}" alt="{{ \Illuminate\Support\Str::before($partner->name, ' ') ?: $partner->name }}">
                        </div>
                        <div>
                            <div class="sm-contact__name">{{ \Illuminate\Support\Str::before($partner->name, ' ') ?: $partner->name }}</div>
                            <div class="sm-thread__mail">{{ $partner->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="sm-thread__body">
                    @forelse ($threadMessages as $message)
                        @php $isMine = $message->sender_id === auth()->id(); @endphp
                        <div class="sm-bubble {{ $isMine ? 'is-mine' : '' }}">
                            <div class="sm-bubble__body">{{ $message->body }}</div>
                            <div class="sm-bubble__meta">{{ $message->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <p class="text-center text-muted mb-0">{{ __('No messages yet. Send the first message.') }}</p>
                    @endforelse
                </div>

                <form class="sm-form" method="POST" action="{{ route('student.messages.store', $partner->id) }}">
                    @csrf
                    <textarea name="body" rows="2" class="form-control" placeholder="{{ __('Write your message...') }}" required></textarea>
                    <div class="text-end">
                        <button type="submit" class="sp-btn sm-send-btn mt-2">{{ __('Send') }}</button>
                    </div>
                </form>
            @else
                <div class="sm-empty">
                    <p class="sm-empty__text">{{ __('Select an instructor to start a chat.') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .sm-header{margin-bottom:12px;}
    .sm-title{font-weight:900;color:#1c1c1c;margin:0;}
    .sm-subtitle{color:#666;margin:0;}
    .sm-grid{display:grid;grid-template-columns:280px 1fr;gap:16px;}
    .sm-list{background:#f7f8fb;border-radius:14px;padding:12px;box-shadow:0 10px 24px rgba(0,0,0,0.05);}
    .sm-list__title{font-weight:800;color:#1c1c1c;margin:0 0 10px;}
    .sm-contacts{display:flex;flex-direction:column;gap:8px;max-height:540px;overflow-y:auto;}
    .sm-contact{display:flex;align-items:center;gap:10px;padding:10px;border-radius:12px;text-decoration:none;border:1px solid transparent;background:#fff;color:#1c1c1c;box-shadow:0 6px 14px rgba(0,0,0,0.05);}
    .sm-contact:hover{border-color:var(--student-brand);}
    .sm-contact.is-active{border-color:var(--student-dark);box-shadow:0 8px 20px rgba(0,0,0,0.08);}
    .sm-contact__avatar{width:48px;height:48px;border-radius:50%;overflow:hidden;border:2px solid var(--student-brand);flex-shrink:0;background:#fff;}
    .sm-contact__avatar img{width:100%;height:100%;object-fit:cover;}
    .sm-contact__meta{flex:1;min-width:0;}
    .sm-contact__name{font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .sm-contact__last{font-size:12px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .sm-badge{background:var(--student-dark);color:#fff;font-weight:800;border-radius:999px;padding:4px 8px;font-size:12px;}

    .sm-thread{background:#fff;border-radius:14px;box-shadow:0 16px 32px rgba(0,0,0,0.08);padding:14px;display:flex;flex-direction:column;min-height:520px;}
    .sm-thread__header{display:flex;align-items:center;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid #eef0f4;margin-bottom:10px;}
    .sm-thread__user{display:flex;align-items:center;gap:10px;}
    .sm-thread__mail{font-size:12px;color:#777;}
    .sm-thread__body{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:10px;padding-right:4px;}
    .sm-bubble{max-width:72%;background:#f3f6fb;border-radius:12px;padding:10px 12px;align-self:flex-start;box-shadow:0 8px 18px rgba(0,0,0,0.05);}
    .sm-bubble.is-mine{background:#e8f3ff;align-self:flex-end;border:1px solid var(--student-dark);}
    .sm-bubble__body{color:#1c1c1c;font-weight:700;margin:0 0 6px;}
    .sm-bubble__meta{font-size:11px;color:#777;margin:0;}
    .sm-form textarea{resize:none;}
    .sm-empty{display:grid;place-items:center;flex:1;}
    .sm-empty__text{color:#777;font-weight:700;}
    .sm-send-btn{background:#0e5c93;color:#fff;border:1px solid #0e5c93;min-width:108px;}
    .sm-send-btn:hover{background:#0b4a78;color:#fff;border-color:#0b4a78;}

    @media(max-width:991px){
        .sm-grid{grid-template-columns:1fr;grid-template-rows:auto auto;}
        .sm-thread{min-height:400px;}
    }
</style>
@endpush
