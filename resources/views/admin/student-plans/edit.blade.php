@extends('admin.master_layout')
@section('title')
    <title>{{ __('Edit Student Plan') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.student-plans.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ __('Paket Düzenle') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.student-plans.index') }}">{{ __('Paketler') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Paket Düzenle') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Paket Düzenle') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.student-plans.update', $plan->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    @include('admin.student-plans._form', ['plan' => $plan])

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

