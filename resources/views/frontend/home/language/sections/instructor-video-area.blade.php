@php
    $featuredInstructorVideos = ($featuredInstructorVideos ?? collect())->values();

    $cards = $featuredInstructorVideos->map(function ($instructor) {
        $profileVideo = data_get($instructor?->instructor_profile, 'intro_video');
        $videoUrl = $profileVideo
            ? (str_starts_with($profileVideo, 'http') ? $profileVideo : asset($profileVideo))
            : null;

        $lowerVideoUrl = strtolower((string) $videoUrl);
        $isDirectVideo = $videoUrl
            && !str_contains($lowerVideoUrl, 'youtube.com')
            && !str_contains($lowerVideoUrl, 'youtu.be')
            && !str_contains($lowerVideoUrl, 'vimeo.com');

        $summary = trim((string) ($instructor->short_bio ?? ''));
        if ($summary === '') {
            $summary = trim(strip_tags((string) ($instructor->bio ?? '')));
        }

        return [
            'id' => $instructor->id,
            'name' => $instructor->first_name,
            'image' => asset($instructor->image),
            'title' => $instructor->job_title ?: __('Ingilizce Egitmeni'),
            'summary' => \Illuminate\Support\Str::limit(
                $summary ?: __('Ogretmenin anlatim tarzini ve enerjisini kisa tanitim videosundan hizlica inceleyebilirsin.'),
                110
            ),
            'detailUrl' => route('instructor-details', [
                'id' => $instructor->id,
                'slug' => \Illuminate\Support\Str::slug($instructor->name),
            ]),
            'videoUrl' => $videoUrl,
            'isDirectVideo' => $isDirectVideo,
        ];
    })->filter(fn ($card) => filled($card['videoUrl']))->values();
@endphp

@if ($cards->count())
    <section class="lf-teacher-vault section-py-110" id="teacher-intro-videos">
        <div class="container">
            <div class="lf-teacher-vault__shell">
                <div class="lf-teacher-vault__header">
                    <div class="lf-teacher-vault__copy">
                        <span class="lf-teacher-vault__eyebrow">{{ __('Ogretmen Tanitim Videolari') }}</span>
                        <h2 class="lf-teacher-vault__title">{{ __('Tum ogretmen videolarini tek alanda incele') }}</h2>
                        <p class="lf-teacher-vault__lead">
                            {{ __('Topluluk akisindan tamamen ayri bu alan, sadece ogretmen tanitim videolari icin tasarlandi. Kartlari karsilastir, videolari izle ve sonra detayli profil sayfasina gec.') }}
                        </p>
                    </div>
                </div>

                <div class="lf-teacher-vault__grid">
                    @foreach ($cards as $card)
                        <article class="lf-teacher-vault__card">
                            <div class="lf-teacher-vault__media">
                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}">

                                <div class="lf-teacher-vault__overlay">
                                    <span class="lf-teacher-vault__tag">{{ __('Tanitim videosu') }}</span>

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

                @if (Route::has('all-instructors'))
                    <div class="lf-teacher-vault__footer">
                        <div class="lf-teacher-vault__footer-copy">
                            <strong>{{ __('Daha fazla egitmen ve daha detayli filtreleme icin listeye gec') }}</strong>
                            <p>
                                {{ __('Bu alanda ilk izlenimi videodan alirsin. Tum egitmenler sayfasinda ise uzmanlik, aksan, deneyim ve ders yaklasimina gore filtreleme yaparak sana en uygun egitmeni secip detayli profiline ulasabilirsin.') }}
                            </p>
                        </div>
                        <div class="lf-teacher-vault__footer-actions">
                            <a href="{{ route('all-instructors') }}" class="lf-teacher-vault__browse">
                                {{ __('Tum egitmenleri gor') }}
                            </a>
                        </div>
                    </div>
                @endif
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
            margin-bottom: 30px;
        }

        .lf-teacher-vault__copy {
            max-width: 860px;
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
            max-width: 760px;
        }

        .lf-teacher-vault__lead {
            margin: 0;
            color: #5a748d;
            font-size: 16px;
            line-height: 1.8;
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

        .lf-teacher-vault__action,
        .lf-teacher-vault__browse {
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
            white-space: nowrap;
        }

        .lf-teacher-vault__link {
            color: #0e5c93;
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .lf-teacher-vault__footer {
            margin-top: 28px;
            padding: 24px 26px;
            border-radius: 26px;
            border: 1px solid rgba(13, 71, 112, 0.08);
            background: linear-gradient(180deg, #f8fbff 0%, #eef5fb 100%);
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .lf-teacher-vault__footer-copy {
            max-width: 780px;
        }

        .lf-teacher-vault__footer-copy strong {
            display: block;
            color: #092947;
            font-size: 22px;
            line-height: 1.15;
            font-weight: 1000;
            margin-bottom: 8px;
        }

        .lf-teacher-vault__footer-copy p {
            margin: 0;
            color: #617b94;
            font-size: 14px;
            line-height: 1.8;
            font-weight: 700;
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

            .lf-teacher-vault__title {
                font-size: 34px;
            }

            .lf-teacher-vault__footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 767px) {
            .lf-teacher-vault__grid {
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

            .lf-teacher-vault__action,
            .lf-teacher-vault__browse {
                width: 100%;
            }
        }
    </style>
@endpush
