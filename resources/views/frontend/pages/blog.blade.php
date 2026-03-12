@extends('frontend.layouts.master')
@section('meta_title', $seo_setting['blog_page']['seo_title'])
@section('meta_description', $seo_setting['blog_page']['seo_description'])

@section('contents')
    <x-frontend.breadcrumb :title="__('Blogs')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Blogs')],
    ]" />

    <section class="blog__post-area section-py-120">
        <div class="container">
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

                    <nav class="pagination__wrap mt-25">
                        {{ $blogs->links() }}
                    </nav>
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
