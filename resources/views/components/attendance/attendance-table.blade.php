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
      <th>Duty On</th>
      <th>Duty Off</th>
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
<script>
  (function() {
      function refreshTable() {
        window['{{ $id }}'].ajax.reload();
      }
      window.refreshTable = refreshTable;
      window['{{ $id }}'] = $('#{{ $id }}').DataTable({
        processing: true,
        serverSide: true,
        fixedColumns: {
          left: 1,
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end toolbar-datatable'>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        scrollX: true,
        order: [1, 'desc'],
        drawCallback: function(settings) {
          $('.toolbar-datatable').html(
            '<button type="button" class="export-excel btn btn-primary btn-sm" >' +
            '<i class="fa fa-file-excel"></i> Export Excel' +
            '</button>'
          );
        },
        ajax: {
          url: '{{ route('attendance.table') }}',
          data: function(d) {
            return $.extend({}, d, {
              nameOrNIK: $('#filterNameOrNIK').val(),
              attendanceType: $('#filterAttendanceType').val(),
              start_date: $('#filterStartDate').val() ? moment($('#filterStartDate').val(), 'DD/MM/YYYY')
                .format('YYYY-MM-DD') : '',
              end_date: $('#filterEndDate').val() ? moment($('#filterEndDate').val(), 'DD/MM/YYYY').format(
                'YYYY-MM-DD') : '',
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
            name: 'schedules.duty_on',
            data: 'duty_on'
          },
          {
            name: 'schedules.duty_off',
            data: 'duty_off'
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

      $('body').on('click', '.export-excel', function(e) {
        var req = new XMLHttpRequest();
        var query = $.param(window['{{ $id }}'].ajax.params());
        req.open("GET", "{{ route('attendance.excel') }}?" + query, true);
        req.responseType = "blob";

        req.onload = function(event) {
          var blob = req.response;
          console.log(blob.size);

          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);

          var filenames_ = "Attendance Report.xls";
          link.download = filenames_;
          document.body.appendChild(link);
          link.click();
        };
        req.send()
      });



    })()
</script>
@endpush