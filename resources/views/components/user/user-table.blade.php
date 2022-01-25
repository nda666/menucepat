<div id="filterCollapse" class="collapse">
  <h5>Filter Pencarian</h5>


  <x-user.user-filter id="{{ $filterFormId }}" on-submit="window.refreshTable()" />
</div>
<table class="table table-striped table-bordered table-sm" id="{{ $id }}" {{ $attributes }}>
  <thead>
    <tr>
      <th>Aksi</th>
      <th>ID</th>
      <th>Nama</th>
      <th>Email</th>
      <th>Device ID</th>
      <th>Divisi</th>
      <th>Sub Divisi</th>
      <th>Company</th>
      <th>Department</th>
      <th>Jabatan</th>
      <th>Lokasi</th>
      <th>Bagian</th>
      <th>Gender</th>
      <th>Alamat</th>
      <th>Gol. Darah</th>
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
        order: [1, 'asc'],
        ajax: {
          url: '{{ route('user.table') }}',
          data: function(d) {
            return $.extend({}, d, {
              nama: $('#filterNama').val(),
              email: $("#filterEmail").val(),
              nik: $('#filterNik').val()
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
                                        <a class="dropdown-item unlock-action" href="#unblock">Buka Lock Login</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete-action" href="#lihat">Hapus</a>
                                    </div>
                                    </div>`
            }
          },
          {
            name: 'id',
            data: 'id'
          },
          {
            name: 'nama',
            data: 'nama'
          },
          {
            name: 'email',
            data: 'email'
          },
          {
            name: 'device_id',
            data: 'device_id'
          },
          {
            name: 'divisi',
            data: 'divisi'
          },
          {
            name: 'subdivisi',
            data: 'subdivisi'
          },
          {
            name: 'company',
            data: 'company'
          },
          {
            name: 'department',
            data: 'department'
          },
          {
            name: 'jabatan',
            data: 'jabatan'
          },
          {
            name: 'lokasi',
            data: 'lokasi'
          },
          {
            name: 'bagian',
            data: 'bagian'
          },

          {
            name: 'sex',
            data: 'sex',
            render: function(val) {
              const gender = ['L', 'P'];
              return gender[val] ? gender[val] : '-'
            }
          },
          {
            name: 'alamat',
            data: 'alamat'
          },
          {
            name: 'blood',
            data: 'blood',
            render: function(val) {
              const blood = ['A', 'B', 'O', 'AB'];
              return blood[val] ? blood[val] : '-'
            }
          },

        ]
      });

    })()
  </script>
@endpush
