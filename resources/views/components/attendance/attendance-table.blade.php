<div id="filterCollapse" class="collapse show">
    <h5>Filter Pencarian</h5>


    <x-attendance.attendance-filter id="{{ $filterFormId }}" on-submit="window.refreshTable()" />
</div>
<table width="100%" class="table table-striped table-bordered table-sm" id="{{ $id }}" {{ $attributes }}>
    <thead>
        <tr>
            <th>Aksi</th>
            <th>ID</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Check Clock</th>
            <th>Tipe Clock</th>
            <th>Tipe</th>
            <th>Latitude</th>
            <th>Longtitude</th>
            <th>Nama Lokasi</th>
            <th>Alasan</th>
            <th>Keterangan</th>
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
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css" />
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
    (function() {
            function refreshTable() {
                window['{{ $id }}'].ajax.reload();
            }
            window.refreshTable = refreshTable;
            window['{{ $id }}'] = $('#{{ $id }}').DataTable({
                processing: true,
                serverSide: true,
                fixedColumns:   {
            left: 1,
        },
                scrollX: true,
                order: [1, 'desc'],
                ajax: {
                    url: '{{ route('attendance.table') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            nameOrNIK: $('#filterNameOrNIK').val(),
                            description: $('#filterDescription').val(),
                            start_date: $('#filterStartDate').val() ? moment( $('#filterStartDate').val() , 'DD/MM/YYYY').format('YYYY-MM-DD') : '',
                            end_date: $('#filterEndDate').val() ? moment( $('#filterEndDate').val() , 'DD/MM/YYYY').format('YYYY-MM-DD') : '',
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
                                        <h6 class="dropdown-header">${row.id} | ${row.user_nama}</h6>
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
                        name: 'attendances.id',
                        data: 'id'
                        
                    },
                    {
                        name: 'nik',
                        data: 'nik'
                    },
                    {
                        name: 'user_nama',
                        data: 'user_nama'
                    },
                    {
                        name: 'attendances.check_clock',
                        data: 'check_clock'
                    },
                    {
                        name: 'attendances.clock_type',
                        data: 'clock_type'
                    },
                    {
                        name: 'attendances.type',
                        data: 'type'
                    },
                    {
                        name: 'attendances.latitude',
                        data: 'latitude'
                    },
                    {
                        name: 'attendances.longtitude',
                        data: 'longtitude'
                    },
                    {
                        name: 'location_name',
                        data: 'location_name'
                    },
                    {
                        name: 'attendances.reason',
                        data: 'reason'
                    },
                    {
                        name: 'attendances.description',
                        data: 'description'
                    },

                ]
            });



        })()
</script>
@endpush