@php
    $featuredInstructorVideos = ($featuredInstructorVideos ?? collect())->take(3)->values();

    $resolveInstructorVideo = function ($instructor) {
        $profileVideo = data_get($instructor?->instructor_profile, 'intro_video');
        $videoUrl = $profileVideo
            ? (str_starts_with($profileVideo, 'http') ? $profileVideo : asset($profileVideo))
            : null;

        $embedUrl = null;
        if ($videoUrl) {
            $lower = strtolower($videoUrl);
            if (str_contains($lower, 'youtube.com') || str_contains($lower, 'youtu.be')) {
                $parts = parse_url($videoUrl) ?: [];
                $host = $parts['host'] ?? '';
                $path = $parts['path'] ?? '';
                $videoId = null;

                if ($host === 'youtu.be') {
                    $videoId = trim($path, '/');
                } else {
                    $query = [];
                    parse_str($parts['query'] ?? '', $query);
                    $videoId = $query['v'] ?? null;

                    if (!$videoId && str_contains($path, '/embed/')) {
                        $segments = explode('/', trim($path, '/'));
                        $embedIndex = array_search('embed', $segments, true);
                        if ($embedIndex !== false && isset($segments[$embedIndex + 1])) {
                            $videoId = $segments[$embedIndex + 1];
                        }
                    }
                }

                if ($videoId) {
                    $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                }
            } elseif (str_contains($lower, 'vimeo.com')) {
                $parts = parse_url($videoUrl) ?: [];
                $segments = array_values(array_filter(explode('/', trim($parts['path'] ?? '', '/'))));
                foreach ($segments as $segment) {
                    if (ctype_digit($segment)) {
                        $embedUrl = 'https://player.vimeo.com/video/' . $segment;
                        break;
                    }
                }
            }
        }

        $summary = trim((string) ($instructor->short_bio ?? ''));
        if ($summary === '') {
            $summary = trim(strip_tags((string) ($instructor->bio ?? '')));
        }

        return [
            'videoUrl' => $videoUrl,
            'embedUrl' => $embedUrl,
            'poster' => asset($instructor->image),
            'summary' => \Illuminate\Support\Str::limit($summary, 150),
            'detailUrl' => route('instructor-details', ['id' => $instructor->id, 'slug' => \Illuminate\Support\Str::slug($instructor->name)]),
        ];
    };

    $primaryInstructor = $featuredInstructorVideos->first();
    $secondaryInstructors = $featuredInstructorVideos->slice(1)->values();
@endphp

@if ($primaryInstructor)
    @php $primaryVideo = $resolveInstructorVideo($primaryInstructor); @endphp

    <section class="lf-instructor-videos section-py-110" id="teacher-intro-videos">
        <div class="container">
            <div class="lf-instructor-videos__shell">
                <div class="lf-instructor-videos__head">
                    <div>
                        <p class="lf-instructor-videos__eyebrow">{{ __('Öğretmen Tanıtım Videoları') }}</p>
                        <h2 class="lf-instructor-videos__title">{{ __('Ders almadan önce eğitmenini videodan tanı') }}</h2>
                    </div>
                    <p class="lf-instructor-videos__lead">
                        {{ __('Her öğretmenin profil videosu ayrı gösterilir. Böylece stilini, enerjisini ve anlatımını dersten önce net biçimde görebilirsin.') }}
                    </p>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-7">
                        <article class="lf-instructor-videos__feature">
                            <div class="lf-instructor-videos__feature-media">
                                @if ($primaryVideo['embedUrl'])
                                    <iframe
                                        src="{{ $primaryVideo['embedUrl'] }}"
                                        title="{{ $primaryInstructor->name }}"
                                        loading="lazy"
                                        allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
                                    </iframe>
                                @elseif ($primaryVideo['videoUrl'])
                                    <video controls preload="metadata" playsinline poster="{{ $primaryVideo['poster'] }}">
                                        <source src="{{ $primaryVideo['videoUrl'] }}">
                                    </video>
                                @else
                                    <div class="lf-instructor-videos__empty">
                                        <i class="fas fa-video"></i>
                                        <span>{{ __('Video bulunamadı') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="lf-instructor-videos__feature-body">
                                <span class="lf-instructor-videos__pill">{{ __('Öne çıkan tanıtım videosu') }}</span>
                                <h3>{{ $primaryInstructor->name }}</h3>
                                <p class="lf-instructor-videos__role">{{ $primaryInstructor->job_title ?: __('Eğitmen') }}</p>
                                <p class="lf-instructor-videos__summary">
                                    {{ $primaryVideo['summary'] ?: __('Bu eğitmenin profil videosunu izleyerek ders tarzını ve anlatım yaklaşımını hızlıca inceleyebilirsin.') }}
                                </p>
                                <a href="{{ $primaryVideo['detailUrl'] }}" class="lf-instructor-videos__cta">
                                    {{ __('Profili incele') }}
                                </a>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-5">
                        <div class="lf-instructor-videos__stack">
                            @foreach ($secondaryInstructors as $instructor)
                                @php $video = $resolveInstructorVideo($instructor); @endphp
                                <article class="lf-instructor-videos__mini">
                                    <div class="lf-instructor-videos__mini-media">
                                        @if ($video['embedUrl'])
                                            <iframe
                                                src="{{ $video['embedUrl'] }}"
                                                title="{{ $instructor->name }}"
                                                loading="lazy"
                                                allowfullscreen
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
                                            </iframe>
                                        @elseif ($video['videoUrl'])
                                            <video controls preload="metadata" playsinline poster="{{ $video['poster'] }}">
                                                <source src="{{ $video['videoUrl'] }}">
                                            </video>
                                        @else
                                            <div class="lf-instructor-videos__empty">
                                                <i class="fas fa-video"></i>
                                                <span>{{ __('Video bulunamadı') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="lf-instructor-videos__mini-body">
                                        <strong>{{ $instructor->name }}</strong>
                                        <span>{{ $instructor->job_title ?: __('Eğitmen') }}</span>
                                        <a href="{{ $video['detailUrl'] }}">{{ __('Profili incele') }}</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@push('styles')
    <style>
        .lf-instructor-videos {
            background:
                radial-gradient(700px circle at 12% 18%, rgba(246, 161, 5, 0.12), transparent 42%),
                linear-gradient(180deg, #f7fbff 0%, #eef5fb 100%);
        }

        .lf-instructor-videos__shell {
            padding: 38px;
            border-radius: 34px;
            border: 1px solid rgba(15, 71, 113, 0.08);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 34px 90px rgba(10, 49, 83, 0.12);
        }

        .lf-instructor-videos__head {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
            gap: 24px;
            align-items: end;
            margin-bottom: 28px;
        }

        .lf-instructor-videos__eyebrow {
            margin: 0 0 10px;
            color: #f6a105;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .lf-instructor-videos__title {
            margin: 0;
            color: #082946;
            font-size: 42px;
            line-height: 1.05;
            font-weight: 1000;
            max-width: 680px;
        }

        .lf-instructor-videos__lead {
            margin: 0;
            color: #56738f;
            font-size: 16px;
            line-height: 1.75;
            font-weight: 700;
        }

        .lf-instructor-videos__feature,
        .lf-instructor-videos__mini {
            height: 100%;
            border-radius: 28px;
            border: 1px solid rgba(10, 61, 101, 0.08);
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            box-shadow: 0 24px 60px rgba(8, 41, 70, 0.1);
            overflow: hidden;
        }

        .lf-instructor-videos__feature-media,
        .lf-instructor-videos__mini-media {
            position: relative;
            background: #dbe8f4;
        }

        .lf-instructor-videos__feature-media {
            aspect-ratio: 16 / 9;
        }

        .lf-instructor-videos__mini-media {
            aspect-ratio: 16 / 10;
        }

        .lf-instructor-videos__feature-media video,
        .lf-instructor-videos__feature-media iframe,
        .lf-instructor-videos__mini-media video,
        .lf-instructor-videos__mini-media iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            object-fit: cover;
            background: #dbe8f4;
        }

        .lf-instructor-videos__feature-body {
            padding: 24px 26px 28px;
        }

        .lf-instructor-videos__pill {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(246, 161, 5, 0.12);
            color: #9a6500;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .lf-instructor-videos__feature-body h3 {
            margin: 16px 0 8px;
            color: #082946;
            font-size: 30px;
            font-weight: 900;
        }

        .lf-instructor-videos__role {
            margin: 0 0 12px;
            color: #0e5c93;
            font-size: 14px;
            font-weight: 900;
        }

        .lf-instructor-videos__summary {
            margin: 0;
            color: #566f88;
            font-size: 15px;
            line-height: 1.8;
            font-weight: 600;
        }

        .lf-instructor-videos__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 18px;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 999px;
            background: #0e5c93;
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease;
        }

        .lf-instructor-videos__cta:hover {
            transform: translateY(-2px);
            background: #083d65;
            color: #fff;
        }

        .lf-instructor-videos__stack {
            display: grid;
            gap: 18px;
            height: 100%;
        }

        .lf-instructor-videos__mini {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
        }

        .lf-instructor-videos__mini-body {
            display: grid;
            gap: 6px;
            padding: 18px 18px 20px;
        }

        .lf-instructor-videos__mini-body strong {
            color: #082946;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.1;
        }

        .lf-instructor-videos__mini-body span {
            color: #56738f;
            font-size: 14px;
            font-weight: 700;
        }

        .lf-instructor-videos__mini-body a {
            margin-top: 4px;
            color: #0e5c93;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .lf-instructor-videos__empty {
            display: grid;
            place-items: center;
            gap: 10px;
            height: 100%;
            color: #56738f;
            font-weight: 800;
        }

        .lf-instructor-videos__empty i {
            color: #f6a105;
            font-size: 26px;
        }

        @media (max-width: 991px) {
            .lf-instructor-videos__shell {
                padding: 24px;
                border-radius: 26px;
            }

            .lf-instructor-videos__head {
                grid-template-columns: 1fr;
            }

            .lf-instructor-videos__title {
                font-size: 32px;
            }
        }

        @media (max-width: 575px) {
            .lf-instructor-videos__title {
                font-size: 28px;
            }

            .lf-instructor-videos__feature-body,
            .lf-instructor-videos__mini-body {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
@endpush
