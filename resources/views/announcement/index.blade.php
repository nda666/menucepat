@extends('adminlte::page')

@section('title', 'Announcement')

@section('content_header')
    <h3>Master Announcement</h3>
@endsection
@section('content')
    <section class="mb-2">
        <div class="btn-group" role="group" aria-label="...">
            <x-announcement.announcement-form grid-id="grid" />
            <button type="button" id="search" class="btn btn-primary" data-toggle="collapse" data-target="#filterCollapse"
                aria-expanded="false" aria-controls="filterCollapse">Filter</button>
        </div>
    </section>
    <x-announcement.announcement-view />
    <div class="card mt-2">
        <div class="card-body">
            <div class="card-text">
                <div class="row">
                    <div class="col-12">
                        <x-announcement.announcement-table id="grid" filter-form-id="filter-form" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        (function() {
            function getRowData(action) {
                const dropdownMenu = $(action).closest('.dropdown-menu');
                const tr = $($(dropdownMenu).data('target')).closest('tr');
                return window.grid.row(tr).data();
            }

            function doAction(ajaxOption, message) {
                ;
                bootbox.confirm({
                    message,
                    callback: async function(result) {
                        /* result is a boolean; true = OK, false = Cancel*/
                        if (result) {
                            const loading = bootbox.dialog({
                                message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Mohon tunggu...</div>',
                                closeButton: false
                            });

                            await $.ajax(ajaxOption);
                            loading.modal('hide');
                        }
                    }
                });
            }

            $('body').on('click', '.edit-action', function(e) {
                const dropdownMenu = $(this).closest('.dropdown-menu');
                const tr = $($(dropdownMenu).data('target')).closest('tr');
                const data = window.grid.row(tr).data();
                $("#modalCreateAnnouncement :input").not('[type=file]').each(function(index, input) {
                    if (input.name === 'user_id') {
                        var newOption = new Option(data.user_nama, data.user_id, false, false);
                        $('#select-user_id').append(newOption).trigger('change');
                    } else {
                        input.name !== '_token' && $(`#formCreateAnnouncement [name="${input?.name}"]`)
                            .val(
                                data[`${input?.name}`]).trigger('change');
                    }
                });
                const startDate = moment(data.start_date).format('YYYY-MM-DD HH:mm');
                const endDate = moment(data.end_date).format('YYYY-MM-DD HH:mm');
                $('#dateRange').daterangepicker({
                    startDate,
                    endDate,
                    timePicker: true,
                    timePicker24Hour: true,
                    drops: 'top',
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });
                $('input[name="_token"]').val('{{ csrf_token() }}');
                $('#modalCreateAnnouncement').modal('show');
            });

            $('body').on('click', '.view-action', function(e) {
                const data = getRowData(this);

                if (data.avatar) {
                    $('.thumb-avatar').prop('src', data.avatar);
                } else {

                    $('.thumb-avatar').prop('src', data.sex === 1 ?
                        "{{ url('storage/images/avatar-f.png') }}" :
                        "{{ url('storage/images/avatar-m.png') }}");
                }

                const blood = ['A', 'B', 'AB', 'O'];
                const gender = ['Laki-laki', 'Perempuan'];
                for (key in data) {
                    const value = data[key];
                    $(`.view-${key}`).text(value);

                    if (key === 'blood') {
                        $(`.view-${key}`).text(blood[value] || '');
                    }

                    if (key === 'sex') {
                        $(`.view-${key}`).text(gender[value] || '');
                    }


                }
                $('#modalViewAnnouncement').modal('show');
            });
            $('body').on('click', '.unlock-action', function(e) {
                const data = getRowData(this);
                const self = this;
                doAction({
                        method: 'POST',
                        url: `{{ url('announcement/unlock') }}/${data.id}`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.grid.ajax.reload(null, false);
                            toastr.success(`${name} berhasil di unlock`)
                        },
                    },
                    `Apakah anda yakin ingin mumbuka lock announcement <strong>${data.nama}</strong>?`
                );
            });

            $('body').on('click', '.delete-action', function(e) {
                const data = getRowData(this);
                const self = this;
                doAction({
                        method: 'DELETE',
                        url: `{{ url('announcement/') }}/${data.id}`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.grid.ajax.reload(null, false);
                            toastr.success(`${name} berhasil dihapus`)
                        },
                    },
                    `Apakah anda yakin ingin menghapus announcement <strong>${data.nama}</strong>?<br>Announcement yang dihapus tidak bisa di pulihkan`
                );

            });

        })()
    </script>
@endsection


@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.Moment', true)
@section('plugins.DateRangePicker', true)
@section('plugins.BootBox', true)
@section('plugins.Toastr', true)
