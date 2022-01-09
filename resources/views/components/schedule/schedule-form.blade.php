<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateSchedule">
  Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateSchedule" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">

    <form id="formCreateSchedule">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Schedule</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input type="hidden" name="id" autocomplete="off">
          <x-pegawai-select2 id="selectUser" name="user_id" required />
          <x-adminlte-input id="codeInput" required name="code" autocomplete="off" placeholder="Kode Jadwal"
            label="Kode Jadwal" />
          <x-adminlte-input id="dateRangeStart" required name="duty_on" autocomplete="off"
            placeholder="Jadwal Mulai - Tanggal & Jam" label="Jadwal Mulai - Tanggal & Jam" />
          <x-adminlte-input id="dateRangeEnd" required name="duty_off" autocomplete="off"
            placeholder="Jadwal Selesai - Tanggal & Jam" label="Jadwal Selesai - Tanggal & Jam" />

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


      $('#dateRangeStart').daterangepicker({
        singleDatePicker: true,
    showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        autoApply: true,
        drops: 'top',
        startDate: moment().set({hour:8,minute:0,second:0,millisecond:0}),
        endDate: moment().startOf('hour').add(24, 'hour'),
        locale: {
          format: 'YYYY-MM-DD HH:mm'
        }
      }).on('show.bs.modal', function(event) {
        event.stopPropagation();
      }).on('change', function(event){
        console.log('as')
        $('#dateRangeEnd').data('daterangepicker').remove()
        $('#dateRangeEnd').daterangepicker({
        autoApply: true,
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        minDate: $('#dateRangeStart').val(),
        maxDate: moment($('#dateRangeStart').val()).add(18, 'hour'),
        timePicker24Hour: true,
        drops: 'top',
        startDate: moment().set({hour:16,minute:0,second:0,millisecond:0}),
        endDate: moment().startOf('hour').add(18, 'hour'),
        locale: {
          format: 'YYYY-MM-DD HH:mm'
        }
      }).on('show.bs.modal', function(event) {
        event.stopPropagation();
      });
      });

      $('#dateRangeEnd').daterangepicker({
        autoApply: true,
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        minDate: $('#dateRangeStart').val(),
        maxDate: moment($('#dateRangeStart').val()).add(18, 'hour'),
        timePicker24Hour: true,
        drops: 'top',
        startDate: moment().set({hour:16,minute:0,second:0,millisecond:0}),
        endDate: moment().startOf('hour').add(18, 'hour'),
        locale: {
          format: 'YYYY-MM-DD HH:mm'
        }
      }).on('show.bs.modal', function(event) {
        event.stopPropagation();
      });


      $('input[name="_token"]').val('{{ csrf_token() }}');
      $('#modalCreateSchedule').on('hide.bs.modal', function() {
        $('#modalCreateSchedule :input').val('').trigger('change');
        $('#modalCreateSchedule select').val('').trigger('change');
        $('#modalCreateSchedule input[name="_token"]').val('{{ csrf_token() }}').trigger(
          'change');
      });
      $('#modalCreateSchedule').on('submit', 'form', function(e) {
        e.preventDefault();
        $('#formCreateSchedule').removeClass('was-validated');
        $(`#formCreateSchedule .invalid-feedback`).remove();
        $(`#formCreateSchedule .is-invalid`).removeClass('is-invalid');
        const data = $(this).serializeArray();
        const id = $('#formCreateSchedule [name="id"]').val();
        const form = new FormData($(this)[0]);
        id && form.append('_method', 'PUT');
        $.ajax({
          url: id ? "{{ url('schedule/') }}/" + id :
          "{{ route('schedule.store') }}", // if id exist use update URL
          method: 'POST', // if id exist use PUT
          data: form,
          processData: false,
          contentType: false,
          success: function(response) {
            toastr.success('Schedule berhasil disimpan');
            @if ($gridId)
              window['{{ $gridId }}'].ajax.reload(null, false);
            @endif
            $('#modalCreateSchedule input, #modalCreateSchedule textarea')
              .val('').trigger('change');
            $('#modalCreateSchedule select').val('').trigger('change');
            $('input[name="_token"]').val('{{ csrf_token() }}');

            id && $('#modalCreateSchedule').modal('hide');
          },
          error: function(xhr) {
            if (xhr?.responseJSON?.message) {
              toastr.error(xhr?.responseJSON?.message);
              if (xhr?.responseJSON?.errors) {
                for (error in xhr.responseJSON.errors) {

                  const name = xhr.responseJSON.errors[error];
                  $(`#modalCreateSchedule [name="${error}"]`).addClass(
                    'is-invalid')
                  $(`#modalCreateSchedule [name="${error}"]`).parent().append(
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