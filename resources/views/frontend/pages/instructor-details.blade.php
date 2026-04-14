@extends('frontend.layouts.master')
@section('meta_title', __('Instructor Details') . ' || ' . $setting->app_name)

@section('contents')
    @php
        $featuredCourse = $courses?->first(fn ($course) => !empty($course?->demo_video_source));

        $profileVideo = data_get($instructor?->instructor_profile, 'intro_video');
        $rawVideoUrl = $profileVideo ?: $featuredCourse?->demo_video_source;
        $videoUrl = $rawVideoUrl
            ? (str_starts_with($rawVideoUrl, 'http') ? $rawVideoUrl : asset($rawVideoUrl))
            : null;

        $videoEmbedUrl = null;
        if ($videoUrl) {
            $lower = strtolower($videoUrl);
            if (str_contains($lower, 'youtube.com') || str_contains($lower, 'youtu.be')) {
                $parts = parse_url($videoUrl) ?: [];
                $host = $parts['host'] ?? '';
                $path = $parts['path'] ?? '';
                $id = null;

                if ($host === 'youtu.be') {
                    $id = trim($path, '/');
                } else {
                    $query = [];
                    parse_str($parts['query'] ?? '', $query);
                    $id = $query['v'] ?? null;

                    if (!$id && str_contains($path, '/embed/')) {
                        $segments = explode('/', trim($path, '/'));
                        $embedIndex = array_search('embed', $segments, true);
                        if ($embedIndex !== false && isset($segments[$embedIndex + 1])) {
                            $id = $segments[$embedIndex + 1];
                        }
                    }
                }

                if ($id) {
                    $videoEmbedUrl = 'https://www.youtube.com/embed/' . $id;
                }
            } elseif (str_contains($lower, 'vimeo.com')) {
                $parts = parse_url($videoUrl) ?: [];
                $path = trim($parts['path'] ?? '', '/');
                $segments = $path ? explode('/', $path) : [];

                $id = null;
                foreach ($segments as $seg) {
                    if (ctype_digit($seg)) {
                        $id = $seg;
                        break;
                    }
                }

                if ($id) {
                    $videoEmbedUrl = 'https://player.vimeo.com/video/' . $id;
                }
            }
        }

        $videoPoster = $instructor?->cover
            ? asset($instructor->cover)
            : ($featuredCourse?->thumbnail ? asset($featuredCourse->thumbnail) : asset($instructor->image));

        $categoryTags = $courses?->pluck('category.name')->filter()->unique()->take(6) ?? collect();

        $aboutMeText = trim((string) ($instructor->short_bio ?? ''));
        if ($aboutMeText === '') {
            $aboutMeText = trim(strip_tags((string) ($instructor->bio ?? '')));
        }
        $displayName = $instructor->first_name ?: $instructor->name;
    @endphp

    <section class="ce-teacher section-py-120">
        <div class="container">
            <div class="row align-items-start ce-teacher__row">
                <div class="col-lg-5">
                    <div class="ce-teacher__header">
                        <div class="ce-teacher__avatar">
                            <img src="{{ asset($instructor->image) }}" alt="img">
                        </div>

                        <div class="ce-teacher__intro">
                            <h2 class="ce-teacher__name">{{ $displayName }}</h2>
                            @if ($categoryTags->count())
                                <div class="ce-teacher__tags">
                                    @foreach ($categoryTags as $tag)
                                        <span class="ce-teacher__tag">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <a class="ce-teacher__book" href="{{ route('student.instructors.schedule', ['instructor' => $instructor->id]) }}">
                            {{ __('Book Lesson') }}
                        </a>
                    </div>

                    <div class="ce-teacher__section">
                        <div class="ce-teacher__section-title">
                            <i class="fas fa-user"></i>
                            <p class="ce-teacher__section-heading">{{ __('About Me') }}</p>
                        </div>
                        <div class="ce-teacher__section-body">
                            @if ($aboutMeText !== '')
                                <p class="mb-0">{{ $aboutMeText }}</p>
                            @else
                                <p class="mb-0 text-muted">{{ __('No biography found.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="ce-teacher__section">
                        <div class="ce-teacher__section-title">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <p class="ce-teacher__section-heading">{{ __('Teaching Style') }}</p>
                        </div>
                        <div class="ce-teacher__section-body">
                            @if ($instructor->bio)
                                {!! clean(nl2br($instructor->bio)) !!}
                            @else
                                <p class="mb-0 text-muted">{{ __('No biography found.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="ce-teacher__video">
                        @if ($videoEmbedUrl)
                            <iframe src="{{ $videoEmbedUrl }}" title="{{ $displayName }}" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
                            </iframe>
                        @elseif ($videoUrl)
                            <video controls preload="metadata" poster="{{ $videoPoster }}">
                                <source src="{{ $videoUrl }}">
                            </video>
                        @else
                            <div class="ce-teacher__video-empty">
                                <i class="fas fa-video"></i>
                                <span>{{ __('No intro video') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .ce-teacher {
            background: #F5F5FF;
        }

        .ce-teacher__row {
            --bs-gutter-x: 48px;
            --bs-gutter-y: 32px;
        }

        .ce-teacher__header {
            display: grid;
            grid-template-columns: 92px 1fr auto;
            gap: 18px;
            align-items: center;
        }

        .ce-teacher__avatar {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            overflow: hidden;
            flex: 0 0 auto;
        }

        .ce-teacher__avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ce-teacher__name {
            margin: 0;
            font-weight: 800;
        }

        .ce-teacher__tags {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ce-teacher__tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: #FAA300;
            color: #fff;
            font-size: 12px;
            line-height: 1;
            font-weight: 600;
        }

        .ce-teacher__book {
            color: #fff;
            border: 1px solid #FAA300;
            background: #FAA300;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 1.1;
            cursor: pointer;
            transition: background-color .3s ease, color .3s ease;
            min-width: 96px;
            text-align: center;
            font-family: var(--tg-heading-font-family);
        }

        .ce-teacher__book:hover {
            color: #FAA300;
            background: #fff;
        }

        .ce-teacher__section {
            margin-top: 28px;
        }

        .ce-teacher__section-title {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
        }

        .ce-teacher__section-title i {
            color: #FAA300;
        }

        .ce-teacher__section-heading {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            font-family: var(--tg-heading-font-family);
        }

        .ce-teacher__section-body {
            font-size: 14px;
            color: #3A3A3A;
            text-align: justify;
        }

        .ce-teacher__form-note {
            display: grid;
            gap: 4px;
            margin-bottom: 16px;
            font-weight: 700;
            color: #6b7280;
        }

        .ce-teacher__form-note span {
            font-weight: 900;
            color: #111827;
        }

        .ce-teacher__video {
            border-radius: 12px;
            overflow: hidden;
            background: #0b0b0f;
            aspect-ratio: 16 / 9;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.12);
        }

        .ce-teacher__video iframe,
        .ce-teacher__video video {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
        }

        .ce-teacher__video-empty {
            height: 100%;
            display: grid;
            place-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 700;
        }

        .ce-teacher__video-empty i {
            color: #FAA300;
            font-size: 32px;
        }

        .ce-teacher__modal {
            border-radius: 16px;
        }

        .ce-teacher__modal .modal-header {
            border-bottom: 0;
            padding: 20px 24px 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .ce-teacher__modal .modal-body {
            padding: 16px 24px 24px;
        }

        .ce-teacher__modal textarea {
            height: 140px;
        }

        .ce-teacher__modal-head {
            display: grid;
            gap: 6px;
        }

        .ce-teacher__modal-sub {
            margin: 0;
            color: #6b7280;
            font-weight: 700;
            font-size: 14px;
        }

        .ce-teacher__form {
            display: grid;
            gap: 14px;
        }

        .ce-teacher__form-note {
            border-radius: 14px;
            background: #fff7e6;
            border: 1px solid #f7ddb3;
            padding: 12px 14px;
        }

        .ce-teacher__form-kicker {
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .ce-teacher__field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 800;
            color: #111827;
            font-size: 13px;
        }

        .ce-teacher__field input,
        .ce-teacher__field textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            font-weight: 700;
            color: #111827;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .ce-teacher__field input:focus,
        .ce-teacher__field textarea:focus {
            outline: none;
            border-color: #FAA300;
            box-shadow: 0 0 0 4px rgba(250, 163, 0, 0.18);
        }

        .ce-teacher__field code {
            color: #ef4444;
        }

        @media (max-width: 991.98px) {
            .ce-teacher__header {
                grid-template-columns: 78px 1fr;
            }

            .ce-teacher__avatar {
                width: 78px;
                height: 78px;
            }

            .ce-teacher__book {
                grid-column: 1 / -1;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('book-lesson-form');
            if (!form) return;

            const labels = {
                title: @json(__('Lesson reservation request')),
                instructor: @json(__('Instructor')),
                name: @json(__('Full Name')),
                phone: @json(__('Phone (WhatsApp)')),
                email: @json(__('Email (optional)')),
                time: @json(__('Preferred time')),
                message: @json(__('Message')),
                page: @json(__('Page')),
            };

            const getValue = (name) => {
                const field = form.querySelector(`[name="${name}"]`);
                return field ? (field.value || '').trim() : '';
            };

            const buildMessage = () => {
                const instructorName = form.dataset.instructor || '';
                const lines = [
                    labels.title,
                    instructorName ? `${labels.instructor}: ${instructorName}` : null,
                    `${labels.name}: ${getValue('name')}`,
                    `${labels.phone}: ${getValue('phone')}`,
                    `${labels.email}: ${getValue('email') || '-'}`,
                    `${labels.time}: ${getValue('preferred_time') || '-'}`,
                    `${labels.message}: ${getValue('message') || '-'}`,
                    `${labels.page}: ${window.location.href.split('#')[0]}`,
                ].filter(Boolean);
                return lines.join('\n');
            };

            const buildWhatsAppUrl = (message) => {
                const waPhone = (form.dataset.waPhone || '').trim();
                const encoded = encodeURIComponent(message);
                if (waPhone) return `https://wa.me/${waPhone}?text=${encoded}`;
                return `https://wa.me/?text=${encoded}`;
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                if (!form.reportValidity()) return;
                const message = buildMessage();
                window.open(buildWhatsAppUrl(message), '_blank', 'noopener,noreferrer');
            });
        })();
    </script>
@endpush

@if (session('instructorQuickContact') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->instructor_contact)
    @php
        $instructorQuickContact = session('instructorQuickContact');
        session()->forget('instructorQuickContact');
    @endphp
    @push('scripts')
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'instructorQuickContact',
                    'contact_info': @json($instructorQuickContact)
                });
            });
        </script>
    @endpush
@endif
