<div id="filterCollapse" class="collapse show">
    <h5>Filter Pencarian</h5>


    <x-setting.setting-filter id="{{ $filterFormId }}" on-submit="window.refreshTable()" />
</div>
<table width="100%" class="table table-striped table-bordered table-sm" id="{{ $id }}" {{ $attributes }}>
    <thead>
        <tr>
            <th>Aksi</th>
            <th>ID</th>
            <th>Kata Kunci</th>
            <th>Nilai</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>


@push('css')
<style>
    #<?="$id "?>th,
    #<?="$id "?>td {
        white-space: nowrap;
    }
</style>
@endpush
@push('js')
<script>
    (function() {
            function refreshTable() {
                window['{{ $id }}'].ajax.reload();
            }
            window.refreshTable = refreshTable;
            window['{{ $id }}'] = $('#{{ $id }}').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                order: [1, 'desc'],
                ajax: {
                    url: '{{ route('setting.table') }}',

                    data: function(d) {
                        return $.extend({}, d, {
                            nama: $('#filterNama').val(),
                        });
                    }
                },
                columns: [{
                        name: 'id',
                        data: 'id',
                        sortable: false,
                        render: function(val, x, row) {
                            let dataAttribut = '';
                            for (key in row) {
                                dataAttribut += `data-${key}="${row[key]}" `;
                            }
                            return `<div class="dropdown show" >
                                    <a class="btn btn-secondary dropdown-toggle"  href="#" role="button" id="dropdownMenuLink${val}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Aksi
                                    </a>

                                    <div  class="dropdown-menu" data-target="#dropdownMenuLink${val}" id="dropdown-menu-${val}" style="position: fixed" aria-labelledby="dropdownMenuLink${val}">
                                        <h6 class="dropdown-header">${row.nama}</h6>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item view-action" href="#lihat">Lihat</a>
                                        <a class="dropdown-item edit-action" href="#edit">Ubah</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete-action" href="#lihat">Hapus</a>
                                    </div>
                                    </div>`
                        }
                    },
                    {
                        name: 'settings.id',
                        data: 'id'
                    },
                    {
                        name: 'settings.key',
                        data: 'key'
                    },
                    {
                        name: 'settings.value',
                        data: 'value'
                    },
                    {
                        name: 'settings.created_at',
                        data: 'created_at'
                    },
                    {
                        name: 'settings.updated_at',
                        data: 'updated_at'
                    },
                    

                ]
            });



        })()
</script>
@endpush