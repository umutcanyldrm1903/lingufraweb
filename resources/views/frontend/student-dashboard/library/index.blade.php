@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $items = $items ?? collect();
        $categories = $categories ?? collect();
        $filteredItems = $filteredItems ?? collect();
        $selectedCategory = $selectedCategory ?? '';
        $palette = ['#dbe7f5', '#f6e9e2', '#f5cfd1', '#f2d9ae', '#e7f3dd', '#f5d6e4', '#f6d7d7', '#d5e8ed', '#d7ead9'];
    @endphp

    <div class="sp-library-page">
        <h2 class="sp-page-title">{{ __('Library') }}</h2>

        @if ($categories->isEmpty())
            <div class="sp-empty-state">
                <div class="sp-empty-state__icon" aria-hidden="true">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="sp-empty-state__text">{{ __('No materials shared yet.') }}</div>
            </div>
        @else
            @if ($selectedCategory === '')
                <div class="sp-library-grid">
                    @foreach ($categories as $index => $category)
                        @php
                            $color = $palette[$index % count($palette)];
                        @endphp
                        <a class="sp-library-tile" href="{{ route('student.library.index', ['category' => $category]) }}"
                            style="background: {{ $color }}">
                            <div class="sp-library-tile__icon" aria-hidden="true">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div class="sp-library-tile__title">{{ $category }}</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sp-library-filter">
                    <a class="sp-library-back" href="{{ route('student.library.index') }}">&larr; {{ __('Back to categories') }}</a>
                    <span class="sp-library-tag">{{ $selectedCategory }}</span>
                </div>

                <div class="sp-library-list">
                    @forelse ($filteredItems as $item)
                        <div class="sp-library-item">
                            <div>
                                <strong>{{ $item->title }}</strong>
                                <p>{{ $item->description }}</p>
                                <small>{{ __('Instructor') }}: {{ \Illuminate\Support\Str::before($item->instructor?->name ?? '', ' ') ?: ($item->instructor?->name ?? '-') }}</small>
                            </div>
                            <div class="sp-library-actions">
                                <a href="{{ !empty($item->is_external) ? $item->file_path : asset($item->file_path) }}" target="_blank" class="sp-library-btn">
                                    {{ __('View File') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="sp-empty-state">
                            <div class="sp-empty-state__icon" aria-hidden="true">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="sp-empty-state__text">{{ __('No items in this category.') }}</div>
                        </div>
                    @endforelse
                </div>
            @endif
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .sp-page-title{margin:6px 0 18px;text-align:center;font-weight:1000;color:var(--student-brand);}
        .sp-library-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px;max-width:980px;margin:0 auto;}
        .sp-library-tile{border-radius:26px;padding:28px 18px;min-height:170px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;text-decoration:none;color:#111827;box-shadow:0 18px 40px rgba(0,0,0,0.12);border:1px solid rgba(17,24,39,.06);transition:transform .18s ease, box-shadow .18s ease;}
        .sp-library-tile:hover{transform:translateY(-2px);box-shadow:0 26px 60px rgba(0,0,0,0.14);color:#111827;}
        .sp-library-tile__icon{width:92px;height:92px;border-radius:50%;background:rgba(255,255,255,.85);display:grid;place-items:center;box-shadow:0 14px 28px rgba(0,0,0,0.10);border:1px solid rgba(17,24,39,.06);}
        .sp-library-tile__icon i{font-size:34px;color:#111827;}
        .sp-library-tile__title{font-weight:1000;font-size:18px;text-align:center;}

        .sp-library-filter{display:flex;align-items:center;justify-content:space-between;gap:10px;max-width:980px;margin:0 auto 16px;}
        .sp-library-back{font-weight:900;color:var(--student-brand);text-decoration:none;}
        .sp-library-tag{font-weight:900;background:#fff2d0;border:1px solid #f6a105;border-radius:999px;padding:6px 12px;}

        .sp-library-list{display:grid;gap:12px;max-width:980px;margin:0 auto;}
        .sp-library-item{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px;border:1px solid #eef2f7;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);}
        .sp-library-item strong{display:block;font-weight:900;color:#111827;}
        .sp-library-item p{margin:4px 0;color:#6b7280;font-weight:700;}
        .sp-library-item small{color:#9ca3af;font-weight:700;}
        .sp-library-actions{display:flex;align-items:center;gap:8px;}
        .sp-library-btn{border-radius:12px;padding:10px 14px;font-weight:900;background:#0e5c93;border:1px solid #0e5c93;color:#fff;text-decoration:none;}
        .sp-library-btn:hover{opacity:.92;color:#fff;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:320px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}

        @media(max-width:991.98px){
            .sp-library-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
            .sp-library-item{flex-direction:column;align-items:flex-start;}
            .sp-library-actions{width:100%;}
        }
        @media(max-width:575.98px){
            .sp-library-grid{grid-template-columns:1fr;gap:14px;}
            .sp-library-tile{min-height:150px;}
        }
    </style>
@endpush
