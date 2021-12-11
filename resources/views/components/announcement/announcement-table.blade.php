<div id="filterCollapse" class="collapse show">
    <h5>Filter Pencarian</h5>


    <x-announcement.announcement-filter id="{{ $filterFormId }}" on-submit="window.refreshTable()" />
</div>
<table width="100%" class="table table-striped table-bordered table-sm" id="{{ $id }}" {{ $attributes }}>
    <thead>
        <tr>
            <th>Aksi</th>
            <th>ID</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Berakhir</th>
            <th>Attachment</th>
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
                    url: '{{ route('announcement.table') }}',

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
                        name: 'announcements.id',
                        data: 'id'
                    },
                    {
                        name: 'announcements.title',
                        data: 'title'
                    },
                    {
                        name: 'announcements.description',
                        data: 'description'
                    },
                    {
                        name: 'announcements.start_date',
                        data: 'start_date'
                    },
                    {
                        name: 'announcements.end_date',
                        data: 'end_date'
                    },
                    {
                        name: 'announcements.attachment',
                        data: 'attachment',
                        sortable: false,
                        render: function(link) {
                            return link ? `<a href="${link}" target="_blank">Attachment</a>` : '';
                        }
                    },

                ]
            });



        })()
    </script>
@endpush
