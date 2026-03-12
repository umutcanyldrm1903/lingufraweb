@extends('admin.master_layout')
@section('title')
    <title>{{ __('Student Plans') }}</title>
@endsection

@section('admin-content')
    @php
        $tableMissing = $tableMissing ?? false;
    @endphp
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Paketler') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Paketler') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Paketler') }}</h4>
                                <div>
                                    @if (!$tableMissing)
                                        <a href="{{ route('admin.student-plans.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> {{ __('Add New') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                @if ($tableMissing)
                                    <div class="alert alert-warning">
                                        {{ __('student_plans tablosu bulunamadı. Önce migration çalıştırın veya SQL import edin.') }}
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Key') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Lessons') }}</th>
                                                <th>{{ __('Months') }}</th>
                                                <th>{{ __('Old') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Per Lesson') }}</th>
                                                <th>{{ __('Active') }}</th>
                                                <th>{{ __('Featured') }}</th>
                                                <th>{{ __('Order') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($plans as $plan)
                                                <tr>
                                                    <td><code>{{ $plan->key }}</code></td>
                                                    <td>
                                                        <div class="font-weight-bold">{{ $plan->title }}</div>
                                                        @if ($plan->display_title)
                                                            <div class="text-muted">{{ $plan->display_title }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $plan->lessons_total }}</td>
                                                    <td>{{ $plan->duration_months }}</td>
                                                    <td>{{ number_format((float) $plan->old_price, 2, ',', '.') }} TL</td>
                                                    <td>{{ number_format((float) $plan->price, 2, ',', '.') }} TL</td>
                                                    <td>
                                                        @php
                                                            $lessonCount = (int) ($plan->lessons_total ?? 0);
                                                            $perLesson = $lessonCount > 0 ? ((float) $plan->price / $lessonCount) : 0;
                                                        @endphp
                                                        {{ $perLesson > 0 ? number_format($perLesson, 2, ',', '.') . ' TL' : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($plan->is_active)
                                                            <div class="badge badge-success">{{ __('Yes') }}</div>
                                                        @else
                                                            <div class="badge badge-danger">{{ __('No') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($plan->featured)
                                                            <div class="badge badge-primary">{{ __('Yes') }}</div>
                                                        @else
                                                            <div class="badge badge-light">{{ __('No') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $plan->sort_order }}</td>
                                                    <td class="text-nowrap">
                                                        <a href="{{ route('admin.student-plans.edit', $plan->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.student-plans.destroy', $plan->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('{{ __('Delete this item?') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted">
                                                        {{ __('No Data!') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $plans->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
