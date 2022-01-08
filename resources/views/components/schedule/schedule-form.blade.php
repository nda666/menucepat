<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateCchedule">
  Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateCchedule" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">

    <form id="formCreateCchedule">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Cchedule</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input type="hidden" name="id" autocomplete="off">
          <x-adminlte-input id="dateRange" required name="date" autocomplete="off"
            placeholder="Tanggal Mulai - Tanggal Selesai" label="Tanggal Mulai - Tanggal Selesai" />
          <x-adminlte-input name="title" label="Judul" required autocomplete="off" placeholder="Judul" />
          <x-adminlte-textarea name="description" label="Deskripsi" required autocomplete="off"
            placeholder="Deskripsi" />

          {{-- <div class="row">
                        <div class="col-md-6">
                            <x-date-picker :id="'start_date'" name="start_date" required autocomplete="off"
                                placeholder="Tanggal Mulai" label="Tanggal Mulai" />
                        </div>
                        <div class="col-md-6">
                            <x-date-picker :id="'end_date'" name="end_date" required autocomplete="off"
                                placeholder="Tanggal Berakhir" label="Tanggal Berakhir" />
                        </div>
                    </div> --}}

          <x-adminlte-input name="attachment" type="file" label="Attachment" autocomplete="off"
            placeholder="Attachment" />

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
      $('#modalCreateCchedule').on('hide.bs.modal', function() {
        $('#modalCreateCchedule :input').val('').trigger('change');
        $('#modalCreateCchedule select').val('').trigger('change');
        $('#modalCreateCchedule input[name="_token"]').val('{{ csrf_token() }}').trigger(
          'change');
      });
      $('#modalCreateCchedule').on('submit', 'form', function(e) {
        e.preventDefault();
        $('#formCreateCchedule').removeClass('was-validated');
        $(`#formCreateCchedule .invalid-feedback`).remove();
        $(`#formCreateCchedule .is-invalid`).removeClass('is-invalid');
        const data = $(this).serializeArray();
        const id = $('#formCreateCchedule [name="id"]').val();
        const form = new FormData($(this)[0]);
        id && form.append('_method', 'PUT');
        let date = $('#dateRange').val();
        const separateDate = date.split(' - ');
        // form.remove('date');
        console.log(form)
        form.append('start_date', separateDate[0]);
        form.append('end_date', separateDate[1]);
        $.ajax({
          url: id ? "{{ url('schedule/') }}/" + id :
          "{{ route('schedule.store') }}", // if id exist use update URL
          method: 'POST', // if id exist use PUT
          data: form,
          processData: false,
          contentType: false,
          success: function(response) {
            toastr.success('Cchedule berhasil disimpan');
            @if ($gridId)
              window['{{ $gridId }}'].ajax.reload(null, false);
            @endif
            $('#modalCreateCchedule input, #modalCreateCchedule textarea')
              .val('').trigger('change');
            $('#modalCreateCchedule select').val('').trigger('change');
            $('input[name="_token"]').val('{{ csrf_token() }}');

            id && $('#modalCreateCchedule').modal('hide');
          },
          error: function(xhr) {
            if (xhr?.responseJSON?.message) {
              toastr.error(xhr?.responseJSON?.message);
              if (xhr?.responseJSON?.errors) {
                for (error in xhr.responseJSON.errors) {

                  const name = xhr.responseJSON.errors[error];
                  $(`#modalCreateCchedule [name="${error}"]`).addClass(
                    'is-invalid')
                  $(`#modalCreateCchedule [name="${error}"]`).parent().append(
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
