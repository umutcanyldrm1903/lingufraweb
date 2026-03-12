@extends('admin.master_layout')
@section('title')
    @php
        $plan = $plan ?? null;
        $isEdit = (bool) ($plan?->id ?? false);
    @endphp
    <title>{{ $isEdit ? __('Edit Student Plan') : __('Add Student Plan') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.student-plans.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $isEdit ? __('Paket Düzenle') : __('Paket Ekle') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.student-plans.index') }}">{{ __('Paketler') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ $isEdit ? __('Paket Düzenle') : __('Paket Ekle') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ $isEdit ? __('Paket Düzenle') : __('Paket Ekle') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ $isEdit ? route('admin.student-plans.update', $plan->id) : route('admin.student-plans.store') }}" method="POST">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    @if (view()->exists('admin.student-plans._form'))
                                        @include('admin.student-plans._form', ['plan' => $plan])
                                    @else
                                        <div class="alert alert-warning">
                                            {{ __('Form partial (admin.student-plans._form) bulunamadı. Lütfen `resources/views/admin/student-plans/_form.blade.php` dosyasını sunucuya yükleyin ve `php artisan view:clear` çalıştırın.') }}
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="text-center col-md-8 offset-md-2">
                                            <x-admin.save-button :text="__('Save')"></x-admin.save-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
