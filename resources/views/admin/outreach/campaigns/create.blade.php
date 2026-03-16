@extends('admin.master_layout')
@section('title')
    <title>Yeni Outreach Kampanyası</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Yeni Outreach Kampanyası</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.outreach-campaigns.index') }}">{{ __('Outreach Bot') }}</a></div>
                    <div class="breadcrumb-item">Yeni Kampanya</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Kampanya Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.store') }}" method="POST">
                            @csrf
                            @include('admin.outreach.campaigns._form')
                            <button type="submit" class="btn btn-primary">Kampanyayı Kaydet</button>
                            <a href="{{ route('admin.outreach-campaigns.index') }}" class="btn btn-light border">Vazgeç</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
