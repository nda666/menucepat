@extends('adminlte::page')

@section('title', 'Setting - ' . env('APP_NAME'))

@section('content_top_nav_left')
<div class="ml-3 navbar-brand" href="#"><i class="fas fa-fw fa-cog "></i> Setting</div>
@endsection
@section('content')
<section class="pt-3 pb-2">
    <div class="btn-group" role="group" aria-label="...">
        <x-setting.setting-form grid-id="grid" />
        <button type="button" id="search" class="btn btn-primary" data-toggle="collapse" data-target="#filterCollapse"
            aria-expanded="false" aria-controls="filterCollapse">Filter</button>
    </div>
</section>
<x-setting.setting-view />
<div class="card mt-2">
    <div class="card-body">
        <div class="card-text">
            <div class="row">
                <div class="col-12">
                    <x-setting.setting-table id="grid" filter-form-id="filter-form" />
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
                $("#modalCreateSetting :input").not('[type=file]').each(function(index, input) {
                    input.name !== '_token' && $(`#formCreateSetting [name="${input?.name}"]`).val(data[`${input?.name}`]).trigger('change');
                });

                $('#modalCreateSetting').modal('show');
            });

            $('body').on('click', '.view-action', function(e) {
                const data = getRowData(this);
                for (key in data) {
                    const value = data[key];
                    $(`.view-${key}`).text(value);
                }
                $('#modalViewSetting').modal('show');
            });

            $('body').on('click', '.delete-action', function(e) {
                const data = getRowData(this);
                const self = this;
                doAction({
                        method: 'DELETE',
                        url: `{{ url('setting/') }}/${data.id}`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.grid.ajax.reload(null, false);
                            toastr.success(`${name} berhasil dihapus`)
                        },
                    },
                    `Apakah anda yakin ingin menghapus setting <strong>${data.nama}</strong>?<br>Setting yang dihapus tidak bisa di pulihkan`
                );

            });

        })()
</script>
@endsection


@section('plugins.Datatables', true)
@section('plugins.BootBox', true)
@section('plugins.Toastr', true)