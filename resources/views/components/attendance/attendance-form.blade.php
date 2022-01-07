<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateAttendance">
  Tambah By System
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateAttendance" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog " role="document">

    <form id="formCreateAttendance">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Attendance</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input type="hidden" name="id" autocomplete="off">
          <x-pegawai-select2 name="user_id" id="selectPegawai" label="Pegawai" required autocomplete="off"
            placeholder="Pegawai" />
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tipe Check Clock</label>
                <div class="d-block px-2 py-2">
                  @foreach ($clockType as $k => $v)
                    <div class="form-check form-check-inline">
                      <input required class="form-check-input" type="radio" name="inlineRadioOptions"
                        id="inlineRadio{{ $k }}" value="{{ $k }}">
                      <label class="form-check-label"
                        for="inlineRadio{{ $k }}">{{ strtoupper($v) }}</label>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <x-adminlte-input id="dateRange" required name="check_clock" autocomplete="off" placeholder="Check Clock"
                label="Check Clock" />
            </div>

          </div>

          <x-adminlte-textarea name="description" label="Keterangan" required autocomplete="off"
            placeholder="Keterangan" />

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('js')

  <script>
    (function() {

      $('#dateRange').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        drops: 'top',
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
          format: 'YYYY-MM-DD HH:mm'
        }
      }).on('show.bs.modal', function(event) {
        event.stopPropagation();
      });


      $('input[name="_token"]').val('{{ csrf_token() }}');
      $('#modalCreateAttendance').on('hide.bs.modal', function() {
        $('#modalCreateAttendance :input').val('').trigger('change');
        $('#modalCreateAttendance select').val('').trigger('change');
        $('#modalCreateAttendance input[name="_token"]').val('{{ csrf_token() }}').trigger(
          'change');
      });
      $('#modalCreateAttendance').on('submit', 'form', function(e) {
        e.preventDefault();
        $('#formCreateAttendance').removeClass('was-validated');
        $(`#formCreateAttendance .invalid-feedback`).remove();
        $(`#formCreateAttendance .is-invalid`).removeClass('is-invalid');
        const data = $(this).serializeArray();
        const id = $('#formCreateAttendance [name="id"]').val();
        const form = new FormData($(this)[0]);
        id && form.append('_method', 'PUT');
        let date = $('#dateRange').val();
        const separateDate = date.split(' - ');
        // form.remove('date');
        console.log(form)
        form.append('start_date', separateDate[0]);
        form.append('end_date', separateDate[1]);
        $.ajax({
          url: id ? "{{ url('attendance/') }}/" + id :
          "{{ route('attendance.store') }}", // if id exist use update URL
          method: 'POST', // if id exist use PUT
          data: form,
          processData: false,
          contentType: false,
          success: function(response) {
            toastr.success('Attendance berhasil disimpan');
            @if ($gridId)
              window['{{ $gridId }}'].ajax.reload(null, false);
            @endif
            $('#modalCreateAttendance input, #modalCreateAttendance textarea')
              .val('').trigger('change');
            $('#modalCreateAttendance select').val('').trigger('change');
            $('input[name="_token"]').val('{{ csrf_token() }}');

            id && $('#modalCreateAttendance').modal('hide');
          },
          error: function(xhr) {
            if (xhr?.responseJSON?.message) {
              toastr.error(xhr?.responseJSON?.message);
              if (xhr?.responseJSON?.errors) {
                for (error in xhr.responseJSON.errors) {

                  const name = xhr.responseJSON.errors[error];
                  $(`#modalCreateAttendance [name="${error}"]`).addClass(
                    'is-invalid')
                  $(`#modalCreateAttendance [name="${error}"]`).parent().append(
                    `<div class="invalid-feedback">${ xhr.responseJSON.errors[error][0]}</div>`
                  )
                }
              }

            } else {
              toastr.error(xhr.statusText);
            }

            if (xhr.code === 422) {

            }

          }
        })
      })
    })()
  </script>
@endpush
