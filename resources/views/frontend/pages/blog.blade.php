@extends('frontend.layouts.master')
@section('meta_title', 'Ingilizce Ders Blogu | Ozel Ders, Speaking ve Is Ingilizcesi Rehberleri')
@section('meta_description', 'Ingilizce ozel ders, online ders, speaking, is ingilizcesi ve sehir bazli programlar hakkinda rehberler, karsilastirmalar ve pratik ipuclari.')
@section('meta_keywords', 'ingilizce ders blogu, ingilizce ozel ders, online ingilizce ozel ders, ingilizce konusma dersi, is ingilizcesi ozel ders')
@section('canonical_url', route('blogs'))

@section('contents')
    <x-frontend.breadcrumb :title="__('Blogs')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Blogs')],
    ]" />

    <section class="blog__post-area section-py-120">
        <div class="container">
            <div class="lf-blog-hub">
                <div class="lf-blog-hub__copy">
                    <span class="lf-blog-hub__eyebrow">{{ __('Editoryal Rehberler') }}</span>
                    <h1>{{ __('Ingilizce ders secimi icin net rehberler') }}</h1>
                    <p>{{ __('Ozel ders mi kurs mu, online ders nasil verimli olur, speaking ve is ingilizcesi nasil planlanir gibi karar anlarinda ihtiyacin olan yazilari burada topladik.') }}</p>
                </div>
                <div class="lf-blog-hub__links">
                    <a href="{{ route('english-private-lessons') }}">{{ __('Ingilizce Ozel Ders') }}</a>
                    <a href="{{ route('english-private-lessons.online') }}">{{ __('Online Ozel Ders') }}</a>
                    <a href="{{ route('english-private-lessons.speaking') }}">{{ __('Konusma Dersi') }}</a>
                    <a href="{{ route('english-private-lessons.business') }}">{{ __('Is Ingilizcesi') }}</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-9 col-lg-8">
                    <div class="row gutter-20">
                        @forelse ($blogs as $blog)
                            <div class="col-md-6">
                                <div class="blog__post-item shine__animate-item">
                                    <div class="blog__post-thumb">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="shine__animate-link blog">
                                            <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
                                        </a>
                                        @if ($blog->category)
                                            <a href="{{ route('blogs', ['category' => $blog->category->slug]) }}"
                                                class="post-tag">{{ $blog->category?->title }}</a>
                                        @endif
                                    </div>
                                    <div class="blog__post-content">
                                        <div class="blog__post-meta">
                                            <ul class="list-wrap">
                                                <li><i class="flaticon-calendar"></i>{{ formatDate($blog->created_at) }}</li>
                                                <li><i class="flaticon-user-1"></i>{{ __('by') }} <a
                                                        href="javascript:;">{{ truncate($blog->author?->name ?? '', 14) }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <h3 class="title">
                                            <a href="{{ route('blog.show', $blog->slug) }}">{{ truncate($blog->title, 60) }}</a>
                                        </h3>
                                        @php
                                            $excerpt = $blog->description;
                                            $excerpt = is_string($excerpt) ? strip_tags($excerpt) : '';
                                        @endphp
                                        @if ($excerpt)
                                            <p>{{ truncate($excerpt, 140) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">{{ __('No blogs found.') }}</div>
                            </div>
                        @endforelse
                    </div>

                    @if ($blogs->count() > 0)
                        <nav class="pagination__wrap mt-25">
                            {{ $blogs->links() }}
                        </nav>
                    @endif
                </div>

                <div class="col-xl-3 col-lg-4">
                    <aside class="blog-sidebar">
                        <div class="blog-widget widget_search">
                            <div class="sidebar-search-form">
                                <form action="{{ route('blogs') }}" method="get">
                                    <input type="text" placeholder="{{ __('Search here') }}" name="search"
                                        value="{{ request('search') }}">
                                    @if (request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    <button type="submit"><i class="flaticon-search"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="blog-widget">
                            <h4 class="widget-title">{{ __('Categories') }}</h4>
                            <div class="shop-cat-list">
                                <ul class="list-wrap">
                                    @foreach ($categories->sortBy('translation.title') as $category)
                                        <li>
                                            <a href="{{ route('blogs', ['category' => $category->slug]) }}"><i
                                                    class="flaticon-angle-right"></i>{{ $category->translation->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="blog-widget">
                            <h4 class="widget-title">{{ __('Popular Post') }}</h4>
                            @forelse ($popularBlogs as $popularBlog)
                                <div class="rc-post-item">
                                    <div class="rc-post-thumb">
                                        <a href="{{ route('blog.show', $popularBlog->slug) }}">
                                            <img class="h_60px" src="{{ asset($popularBlog->image) }}" alt="img">
                                        </a>
                                    </div>
                                    <div class="rc-post-content">
                                        <span class="date"><i class="flaticon-calendar"></i>
                                            {{ formatDate($popularBlog->created_at) }}</span>
                                        <h4 class="title"><a
                                                href="{{ route('blog.show', $popularBlog->slug) }}">{{ truncate($popularBlog->title, 30) }}</a>
                                        </h4>
                                    </div>
                                </div>
                            @empty
                                <p>{{ __('No popular post yet') }}.</p>
                            @endforelse
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .lf-blog-hub {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.9fr);
            gap: 22px;
            margin-bottom: 32px;
            padding: 30px;
            border-radius: 28px;
            background: linear-gradient(135deg, #0c4f7f, #0a3f67);
            color: #fff;
            box-shadow: 0 22px 48px rgba(10, 39, 63, 0.16);
        }

        .lf-blog-hub__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .lf-blog-hub__eyebrow::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #f6a105;
        }

        .lf-blog-hub h1 {
            margin: 0 0 12px;
            color: #fff;
            font-size: clamp(30px, 3.2vw, 42px);
            line-height: 1.06;
            font-weight: 1000;
        }

        .lf-blog-hub p {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.8;
            font-weight: 600;
        }

        .lf-blog-hub__links {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            align-content: center;
        }

        .lf-blog-hub__links a {
            display: flex;
            align-items: center;
            min-height: 74px;
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            font-weight: 800;
            line-height: 1.5;
        }

        .lf-blog-hub__links a:hover {
            background: rgba(255, 255, 255, 0.16);
        }

        @media (max-width: 991.98px) {
            .lf-blog-hub,
            .lf-blog-hub__links {
                grid-template-columns: 1fr;
            }

            .lf-blog-hub {
                padding: 24px;
            }
        }
    </style>
@endpush
