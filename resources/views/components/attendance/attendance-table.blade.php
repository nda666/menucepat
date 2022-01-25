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
      <th>Photo</th>
      <th>Capture</th>
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

    .ekko-custom-width {
      max-width: '80vw' !important;
      width: '80vw' !important;
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
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end toolbar-datatable'>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        scrollX: true,
        order: [1, 'desc'],
        drawCallback: function(settings) {
          $('.toolbar-datatable').html(`
          <a class="popover-dismiss mr-2" data-placement="top" data-toggle="popover" data-trigger="focus" title="Info" data-content="Lebar <b>photo pegawai</b> dan <b>capture absensi</b> di-resize sebesar 50px agar ukuran file excel dengan photo tidak menjadi lebih besar. "><i class="fa fa-info-circle fa-sm"></i></a>

          <div class="btn-group" role="group" aria-label="Basic example">
            
            <button type="button" class="export-excel btn btn-primary btn-sm" >
            <i class="fa fa-file-excel"></i> Export Excel
            </button>
            <button type="button" class="export-excel-without-photo btn btn-primary btn-sm" >
            <i class="fa fa-file-excel"></i> Export Excel - Tanpa Photo
            </button></div>
          `);
          $('.popover-dismiss').popover({
            trigger: 'hover',
            html: true
          })
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
            name: 'user_image',
            data: 'user_image',
            render: function(img, x, row) {
              return `<a href="${row.user_image}" data-max-width="1000" data-second="${row.image}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4"><img src="${img}" width="50px" height="50px" class="rounded-sm"></a>`
            }
          },
          {
            name: 'image',
            data: 'image',
            render: function(img, x, row) {
              return `<a href="${row.user_image}" data-second="${row.image}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4"><img src="${img}" width="50px" height="50px" class="rounded-sm"></a>`
            }
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

      $('body').on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        let _this = $(this);
        let div = $('<div></div>');
        div.css({
          display: 'flex',
          height: '80vh',
          flexDirection: 'row',
          justifyContent: 'center',
          alignItems: 'center'
        });
        div.html(
          `<div class="text-center" style="width: 50%;"><img class="img-fluid" style="max-height: 80vh" src="${_this.attr('href')}" /></div><div class="text-center" style="width: 50%;"><img class="img-fluid" style="max-height: 80vh" src="${_this.attr('data-second')}"/></div>`
        )

        var dialog = bootbox.dialog({
          message: div[0],
          closeButton: false,
          size: 'xl',
          backdrop: true
        });
      });


      function makeLoading(isLoading = true) {
        if (isLoading) {

          $('.export-excel-without-photo, .export-excel').prop('disabled', true);
          $('.export-excel-without-photo').html(
            `<div class="spinner-grow spinner-grow-sm text-light" role="status"><span class="sr-only">Loading...</span></div> Export Excel - Tanpa Photo`
          );
          $('.export-excel').html(
            `<div class="spinner-grow spinner-grow-sm text-light" role="status"><span class="sr-only">Loading...</span></div> Export Excel`
          );
        } else {
          $('.export-excel-without-photo').html(`<i class="fa fa-file-excel"></i> Export Excel - Tanpa Photo`);
          $('.export-excel').html(`<i class="fa fa-file-excel"></i> Export Excel`);
          $('.export-excel-without-photo, .export-excel').prop('disabled', false);
        }
      }

      function exportExcel($withoutImage = false) {
        makeLoading();
        var req = new XMLHttpRequest();
        var query = $.param({
          ...window['{{ $id }}'].ajax.params(),
          without_image: $withoutImage ? 1 : 0,
        });
        req.open("GET", "{{ route('attendance.excel') }}?" + query, true);
        req.responseType = "blob";
        req.onreadystatechange = function() {
          if (req.readyState == 4) {

            makeLoading(false);
          }
        };
        req.onload = function(event) {
          var blob = req.response;

          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);

          var filenames_ = "Attendance Report.xls";
          link.download = filenames_;
          document.body.appendChild(link);
          link.click();
        };
        req.send()
      }

      $('body').on('click', '.export-excel-without-photo', function(event) {
        event.preventDefault();
        exportExcel(true);
      });

      $('body').on('click', '.export-excel', function(e) {
        exportExcel();
      });



    })()
  </script>
@endpush
