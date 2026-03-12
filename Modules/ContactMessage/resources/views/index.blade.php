@extends('admin.master_layout')
@section('title')
    <title>{{ __('Contact Message') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Contact Message') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end">
                                    <a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#customMailModal">
                                        <i class="fas fa-paper-plane"></i> {{ __('Send Mail') }}
                                    </a>
                                </div>
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Created at') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($messages as $index => $message)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ html_decode($message->name) }}</td>
                                                    <td><a
                                                            href="mailto:{{ html_decode($message->email) }}">{{ html_decode($message->email) }}</a>
                                                    </td>
                                                    <td>{{ $message->created_at->format('h:iA, d M Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.contact-message', $message->id) }}"
                                                            class="btn btn-success btn-sm"><i class="fa fa-eye"
                                                                aria-hidden="true"></i></a>
                                                        @if (!empty($message->email))
                                                            <a href="javascript:;" class="btn btn-primary btn-sm js-send-mail"
                                                                data-toggle="modal" data-target="#sendMailModal"
                                                                data-id="{{ $message->id }}"
                                                                data-email="{{ html_decode($message->email) }}">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </a>
                                                        @endif
                                                        <a onclick="deleteData({{ $message->id }})" href="javascript:;"
                                                            data-toggle="modal" data-target="#deleteModal"
                                                            class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('')" route="" create="no"
                                                    :message="__('No data found!')" colspan="5"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <x-admin.delete-modal />

    <div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog" aria-labelledby="sendMailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendMailModalLabel">{{ __('Send Mail') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="sendMailForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="sendMailTo" name="to_email" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Subject') }} <code>*</code></label>
                            <input type="text" class="form-control" name="subject" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Message') }} <code>*</code></label>
                            <textarea class="form-control" rows="7" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Send Mail') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customMailModal" tabindex="-1" role="dialog" aria-labelledby="customMailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customMailModalLabel">{{ __('Send Mail') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.contact-message-send-custom') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('Email') }} <code>*</code></label>
                            <input type="email" class="form-control" name="to_email" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Subject') }} <code>*</code></label>
                            <input type="text" class="form-control" name="subject" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Message') }} <code>*</code></label>
                            <textarea class="form-control" rows="7" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Send Mail') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ url('/admin/contact-message-delete/') }}' + "/" + id)
            }

            $(document).on('click', '.js-send-mail', function() {
                const id = $(this).data('id');
                const email = $(this).data('email');
                $('#sendMailTo').val(email || '');
                $('#sendMailForm').attr('action', '{{ url('/admin/contact-message-send-mail/') }}' + "/" + id);
            });
        </script>
    @endpush
@endsection
