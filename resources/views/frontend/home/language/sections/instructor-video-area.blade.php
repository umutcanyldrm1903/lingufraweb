@php
    $featuredInstructorVideos = ($featuredInstructorVideos ?? collect())->values();

    $cards = $featuredInstructorVideos->map(function ($instructor) {
        $profileVideo = data_get($instructor?->instructor_profile, 'intro_video');
        $videoUrl = $profileVideo
            ? (str_starts_with($profileVideo, 'http') ? $profileVideo : asset($profileVideo))
            : null;

        $isDirectVideo = $videoUrl && !str_contains(strtolower($videoUrl), 'youtube.com')
            && !str_contains(strtolower($videoUrl), 'youtu.be')
            && !str_contains(strtolower($videoUrl), 'vimeo.com');

        $summary = trim((string) ($instructor->short_bio ?? ''));
        if ($summary === '') {
            $summary = trim(strip_tags((string) ($instructor->bio ?? '')));
        }

        return [
            'id' => $instructor->id,
            'name' => $instructor->name,
            'image' => asset($instructor->image),
            'title' => $instructor->job_title ?: __('İngilizce Eğitmeni'),
            'summary' => \Illuminate\Support\Str::limit($summary ?: __('Öğretmenin anlatım tarzını, enerjisini ve profil yaklaşımını kısa videodan inceleyebilirsin.'), 110),
            'detailUrl' => route('instructor-details', ['id' => $instructor->id, 'slug' => \Illuminate\Support\Str::slug($instructor->name)]),
            'videoUrl' => $videoUrl,
            'isDirectVideo' => $isDirectVideo,
        ];
    })->filter(fn ($card) => filled($card['videoUrl']))->values();

    $videoCount = $cards->count();
@endphp

@if ($videoCount)
    <section class="lf-teacher-vault section-py-110" id="teacher-intro-videos">
        <div class="container">
            <div class="lf-teacher-vault__shell">
                <div class="lf-teacher-vault__header">
                    <div class="lf-teacher-vault__copy">
                        <span class="lf-teacher-vault__eyebrow">{{ __('Öğretmen Tanıtım Videoları') }}</span>
                        <h2 class="lf-teacher-vault__title">{{ __('Tüm öğretmen videolarını tek alanda incele') }}</h2>
                        <p class="lf-teacher-vault__lead">
                            {{ __('Topluluk alanından ayrı, sadece öğretmen videolarına ayrılmış temiz bir vitrin. Öğretmenleri karşılaştır, stilini gör ve sonra profil detayına geç.') }}
                        </p>
                    </div>

                    <div class="lf-teacher-vault__meta">
                        <div class="lf-teacher-vault__metric">
                            <strong>{{ $videoCount }}</strong>
                            <span>{{ __('aktif öğretmen videosu') }}</span>
                        </div>
                        <div class="lf-teacher-vault__metric">
                            <strong>{{ __('Tamamı ayrı') }}</strong>
                            <span>{{ __('topluluk akışından bağımsız') }}</span>
                        </div>
                    </div>
                </div>

                <div class="lf-teacher-vault__grid">
                    @foreach ($cards as $card)
                        <article class="lf-teacher-vault__card">
                            <div class="lf-teacher-vault__media">
                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}">

                                <div class="lf-teacher-vault__overlay">
                                    <span class="lf-teacher-vault__tag">{{ __('Tanıtım videosu') }}</span>

                                    @if ($card['isDirectVideo'])
                                        <button
                                            type="button"
                                            class="lf-teacher-vault__play js-lf-open-video-modal"
                                            data-video-src="{{ $card['videoUrl'] }}"
                                            aria-label="{{ __('Videoyu izle') }}"
                                        >
                                            <i class="fas fa-play" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <a
                                            href="{{ $card['detailUrl'] }}"
                                            class="lf-teacher-vault__play"
                                            aria-label="{{ __('Profilden videoyu izle') }}"
                                        >
                                            <i class="fas fa-play" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="lf-teacher-vault__body">
                                <div class="lf-teacher-vault__person">
                                    <strong>{{ $card['name'] }}</strong>
                                    <span>{{ $card['title'] }}</span>
                                </div>

                                <p class="lf-teacher-vault__summary">{{ $card['summary'] }}</p>

                                <div class="lf-teacher-vault__actions">
                                    @if ($card['isDirectVideo'])
                                        <button
                                            type="button"
                                            class="lf-teacher-vault__action js-lf-open-video-modal"
                                            data-video-src="{{ $card['videoUrl'] }}"
                                        >
                                            {{ __('Videoyu izle') }}
                                        </button>
                                    @else
                                        <a href="{{ $card['detailUrl'] }}" class="lf-teacher-vault__action">
                                            {{ __('Profilden izle') }}
                                        </a>
                                    @endif

                                    <a href="{{ $card['detailUrl'] }}" class="lf-teacher-vault__link">
                                        {{ __('Profili incele') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

@push('styles')
    <style>
        .lf-teacher-vault {
            background:
                radial-gradient(800px circle at 10% 12%, rgba(246, 161, 5, 0.1), transparent 38%),
                radial-gradient(900px circle at 92% 8%, rgba(14, 92, 147, 0.08), transparent 44%),
                linear-gradient(180deg, #f7fbff 0%, #eef4f9 100%);
        }

        .lf-teacher-vault__shell {
            padding: 42px;
            border-radius: 34px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(13, 71, 112, 0.08);
            box-shadow: 0 26px 80px rgba(9, 37, 61, 0.1);
        }

        .lf-teacher-vault__header {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            align-items: end;
            margin-bottom: 30px;
        }

        .lf-teacher-vault__copy {
            max-width: 760px;
        }

        .lf-teacher-vault__eyebrow {
            display: inline-flex;
            margin-bottom: 12px;
            color: #f6a105;
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .lf-teacher-vault__title {
            margin: 0 0 12px;
            color: #092947;
            font-size: 44px;
            line-height: 1.04;
            font-weight: 1000;
            max-width: 740px;
        }

        .lf-teacher-vault__lead {
            margin: 0;
            color: #5a748d;
            font-size: 16px;
            line-height: 1.8;
            font-weight: 700;
        }

        .lf-teacher-vault__meta {
            display: grid;
            gap: 14px;
            min-width: 240px;
        }

        .lf-teacher-vault__metric {
            padding: 18px 20px;
            border-radius: 22px;
            background: linear-gradient(180deg, #f8fbff 0%, #edf4fa 100%);
            border: 1px solid rgba(13, 71, 112, 0.08);
        }

        .lf-teacher-vault__metric strong {
            display: block;
            color: #0e5c93;
            font-size: 22px;
            line-height: 1.1;
            font-weight: 1000;
        }

        .lf-teacher-vault__metric span {
            display: block;
            margin-top: 6px;
            color: #67829b;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 700;
        }

        .lf-teacher-vault__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .lf-teacher-vault__card {
            overflow: hidden;
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(13, 71, 112, 0.08);
            box-shadow: 0 20px 54px rgba(12, 42, 69, 0.08);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .lf-teacher-vault__card:hover {
            transform: translateY(-4px);
            box-shadow: 0 28px 64px rgba(12, 42, 69, 0.12);
        }

        .lf-teacher-vault__media {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #dce8f2;
        }

        .lf-teacher-vault__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .lf-teacher-vault__overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 18px;
            background: linear-gradient(180deg, rgba(7, 17, 30, 0.04) 0%, rgba(7, 17, 30, 0.18) 48%, rgba(7, 17, 30, 0.58) 100%);
        }

        .lf-teacher-vault__tag {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #123f6a;
            font-size: 11px;
            font-weight: 1000;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .lf-teacher-vault__play {
            width: 58px;
            height: 58px;
            display: inline-grid;
            place-items: center;
            border: 0;
            border-radius: 50%;
            background: #ffffff;
            color: #0e5c93;
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.18);
            text-decoration: none;
            transition: transform .18s ease, background .18s ease, color .18s ease;
        }

        .lf-teacher-vault__play:hover {
            transform: scale(1.05);
            background: #0e5c93;
            color: #fff;
        }

        .lf-teacher-vault__body {
            padding: 22px 22px 24px;
        }

        .lf-teacher-vault__person strong {
            display: block;
            color: #092947;
            font-size: 24px;
            line-height: 1.1;
            font-weight: 1000;
        }

        .lf-teacher-vault__person span {
            display: block;
            margin-top: 6px;
            color: #54718a;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 700;
        }

        .lf-teacher-vault__summary {
            margin: 14px 0 0;
            color: #617b94;
            font-size: 14px;
            line-height: 1.75;
            font-weight: 700;
            min-height: 74px;
        }

        .lf-teacher-vault__actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        .lf-teacher-vault__action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border: 0;
            border-radius: 999px;
            background: #0e5c93;
            color: #fff;
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .lf-teacher-vault__link {
            color: #0e5c93;
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
        }

        @media (max-width: 1199px) {
            .lf-teacher-vault__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991px) {
            .lf-teacher-vault__shell {
                padding: 24px;
                border-radius: 26px;
            }

            .lf-teacher-vault__header {
                flex-direction: column;
                align-items: stretch;
            }

            .lf-teacher-vault__meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                min-width: 0;
            }

            .lf-teacher-vault__title {
                font-size: 34px;
            }
        }

        @media (max-width: 767px) {
            .lf-teacher-vault__grid {
                grid-template-columns: 1fr;
            }

            .lf-teacher-vault__meta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .lf-teacher-vault__title {
                font-size: 28px;
            }

            .lf-teacher-vault__body {
                padding-left: 16px;
                padding-right: 16px;
            }

            .lf-teacher-vault__actions {
                align-items: flex-start;
                flex-direction: column;
            }

            .lf-teacher-vault__action {
                width: 100%;
            }
        }
    </style>
@endpush
