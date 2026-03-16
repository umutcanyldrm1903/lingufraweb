@extends('admin.master_layout')
@section('title')
    <title>Outreach Kampanyasını Düzenle</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Outreach Kampanyasını Düzenle</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.outreach-campaigns.index') }}">{{ __('Outreach Bot') }}</a></div>
                    <div class="breadcrumb-item">{{ $outreachCampaign->name }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Kampanya Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.update', $outreachCampaign) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('admin.outreach.campaigns._form')
                            <button type="submit" class="btn btn-primary">Kampanyayı Güncelle</button>
                            <a href="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" class="btn btn-light border">Geri Dön</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
