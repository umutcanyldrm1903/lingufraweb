<section class="cowboy-faq section-py-120">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-6">
                <div class="cowboy-faq__media">
                    <img src="{{ asset($faqSection?->global_content?->image) }}" alt="faq" class="cf-img">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="cowboy-faq__copy">
                    <p class="eyebrow">{{ $faqSection?->content?->short_title }}</p>
                    <h2 class="cf-title">{!! clean(processText($faqSection?->content?->title)) !!}</h2>
                    <p class="cf-lead">{!! clean(processText($faqSection?->content?->description)) !!}</p>
                    <div class="accordion cowboy-accordion" id="cowboyFaq">
                        @foreach ($faqs as $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="faq{{ $faq->id }}">
                                        {{ $faq?->question }}
                                    </button>
                                </h2>
                                <div id="faq{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#cowboyFaq">
                                    <div class="accordion-body">
                                        <p>{{ $faq?->answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .cowboy-faq {background:#fef7f1;}
    .cf-title {font-weight:800;font-size:32px;color:#1c1c1c;margin-bottom:10px;}
    .cf-lead {color:#444;margin-bottom:16px;}
    .cf-img {width:100%;border-radius:18px;box-shadow:0 18px 48px rgba(0,0,0,0.08);}
    .cowboy-accordion .accordion-item {border:1px solid #f1dfd4;border-radius:12px;margin-bottom:10px;overflow:hidden;background:#fff;}
    .cowboy-accordion .accordion-button {font-weight:700;color:#1f1f1f;box-shadow:none;background:#fff;padding:14px 16px;}
    .cowboy-accordion .accordion-button:not(.collapsed) {color:#e95133;background:#fff3eb;}
    .cowboy-accordion .accordion-body {color:#444;padding:0 16px 14px;}
    @media(max-width:991px){.cf-title{font-size:26px;}}
</style>
@endpush
