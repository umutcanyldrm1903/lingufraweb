@extends('admin.master_layout')
@section('title')
<title>{{ __('Message Details') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Message Details') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tr>
                                        <td>{{ __('Name') }}</td>
                                        <td>{{ html_decode($message->name) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Email') }}</td>
                                        <td><a href="mailto:{{ html_decode($message->email) }}">{{ html_decode($message->email) }}</a></td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Subject') }}</td>
                                        <td>{{ html_decode($message->subject) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Message') }}</td>
                                        <td>{!! clean($message->message) !!}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Created at') }}</td>
                                        <td>{{ $message->created_at->format('h:iA, d M Y') }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('Action') }}</td>
                                        <td>
                                            @if (!empty($message->email))
                                                <a href="javascript:;" data-toggle="modal" data-target="#sendMailModal"
                                                    class="btn btn-primary btn-sm mr-1"><i class="fas fa-paper-plane"></i>
                                                    {{ __('Send Mail') }}</a>
                                            @endif
                                            <a onclick="deleteData({{ $message->id }})" href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> {{ __('Delete') }}</a>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <x-admin.delete-modal />

    @if (!empty($message->email))
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
                    <form action="{{ route('admin.contact-message-send-mail', $message->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="to_email" value="{{ html_decode($message->email) }}" required>
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
    @endif

    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ url("/admin/contact-message-delete/") }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
