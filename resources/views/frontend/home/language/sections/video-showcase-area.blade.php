@php
    $videoSet = $videoSet ?? [1, 2, 3];
    $videoTitle = $videoTitle ?? __('Student Stories');
    $videoSubtitle = $videoSubtitle ?? __('Real videos from LinguFranca experience');
    $videoPrefix = $videoPrefix ?? __('Clip');
@endphp

<section class="lf-video-showcase section-py-100">
    <div class="container">
        <div class="lf-video-showcase__head">
            <span class="eyebrow">{{ __('Video') }}</span>
            <h2 class="lf-video-showcase__title">{{ $videoTitle }}</h2>
            <p class="lf-video-showcase__subtitle">{{ $videoSubtitle }}</p>
        </div>

        <div class="lf-video-showcase__grid">
            @foreach ($videoSet as $videoNo)
                @php
                    $num = str_pad((string) ((int) $videoNo), 2, '0', STR_PAD_LEFT);
                    $file = 'uploads/website-videos/home-showcase/home-video-' . $num . '.mp4';
                    $poster = 'uploads/website-videos/home-showcase/posters/home-video-' . $num . '.jpg';
                @endphp
                <article class="lf-video-showcase__card js-lf-video-card">
                    <div class="lf-video-showcase__media">
                        <video
                            class="lf-video-showcase__video js-lf-autoplay-video"
                            autoplay
                            muted
                            loop
                            playsinline
                            preload="metadata"
                            poster="{{ asset($poster) }}"
                            data-src="{{ asset($file) }}"
                            data-video-id="{{ $num }}"
                            aria-label="{{ __('LinguFranca video') }} {{ $num }}"
                        ></video>
                    </div>
                    <button
                        type="button"
                        class="lf-video-showcase__expand js-lf-open-video-modal"
                        data-video-src="{{ asset($file) }}"
                        aria-label="{{ __('Videoyu buyut ve izle') }}"
                    >
                        <i class="fas fa-expand-alt" aria-hidden="true"></i>
                        <span>{{ __('Buyut') }}</span>
                    </button>
                    <span class="lf-video-showcase__badge">{{ __('Auto Play') }}</span>
                    <span class="lf-video-showcase__meta">{{ $videoPrefix }} {{ $num }}</span>
                </article>
            @endforeach
        </div>
    </div>
</section>
