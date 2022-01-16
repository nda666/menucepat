<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateUser">
  Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
  aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">

    <form id="formCreateUser">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="row">

            {{ csrf_field() }}
            <input type="hidden" name="id" autocomplete="off">
            <x-adminlte-input name="nama" type="text" label="Nama" required autocomplete="off" placeholder="Nama"
              fgroup-class="col-md-4" />

            <x-adminlte-input type="file" label="Avatar/Photo" name="avatar" class="form-control" id="jabatan"
              placeholder="Avatar/Photo" fgroup-class="col-md-4" />
            <x-adminlte-input name="email" type="email" label="Email" required autocomplete="off"
              placeholder="mail@example.com" fgroup-class="col-md-4" />
          </div>
          <div class="row">
            <x-adminlte-input label="Whatsapp" required autocomplete="off" name="whatsapp" class="form-control"
              id="whatsapp" placeholder="Whatsapp" fgroup-class="col-md-4" />
            <x-adminlte-input label="Device ID" autocomplete="off" name="device_id" class="form-control"
              id="device_id" placeholder="Device ID" fgroup-class="col-md-4" />
            <x-adminlte-input label="NIK" required autocomplete="off" name="nik" class="form-control" id="nik"
              placeholder="NIK" fgroup-class="col-md-4" />
          </div>
          <div class="row">
            <x-adminlte-input type="password" label="Password" name="password" class="form-control" id="password"
              placeholder="Password" required autocomplete="off" fgroup-class="col-md-6" />
            <x-adminlte-input type="password" name="password_conf" class="form-control" id="password_conf"
              placeholder="Ketik Ulang Password" required autocomplete="off" label="Ketik Ulang Password"
              fgroup-class="col-md-6" />
          </div>



          <div class="row">

            <x-date-picker :id="'tglLahir'" name="tgl_lahir" required autocomplete="off" placeholder="Tanggal lahir"
              label="Tanggal Lahir" fgroup-class="col-md-4" />
            <x-adminlte-input label="Kota Kelahiran" name="kota_lahir" required autocomplete="off"
              class="form-control" id="kota_lahir" placeholder="Kota Kelahiran" fgroup-class="col-md-4" />
            <x-adminlte-select name="sex" required autocomplete="off" label="Gender" fgroup-class="col-md-2">
              <option disabled value="" selected>Pilih Gender</option>
              @foreach ($sexType as $v)
                <option value="{{ $v->value }}">{{ $v->description }}</option>
              @endforeach

            </x-adminlte-select>
            <x-adminlte-select name="blood" required autocomplete="off" label="Gol. Darah"
              fgroup-class="col-md-2">
              <option disabled value="" selected>Pilih Gol. Darah</option>
              @foreach ($bloodType as $v)
                <option value="{{ $v->value }}">{{ $v->description }}</option>
              @endforeach
            </x-adminlte-select>
          </div>

          <div class="row">
            <x-adminlte-textarea name="alamat" id="alamat" label="Alamat" rows=3 placeholder="Alamat"
              fgroup-class="col-md-12">
            </x-adminlte-textarea>
          </div>

          <div class="row">
            <x-adminlte-input label="Divisi" required autocomplete="off" name="divisi" class="form-control"
              id="divisi" placeholder="Divisi" fgroup-class="col-md-6" />
            <x-adminlte-input label="Sub Divisi" required autocomplete="off" name="subdivisi" class="form-control"
              id="subdivisi" placeholder="Sub Divisi" required autocomplete="off" fgroup-class="col-md-6" />



          </div>

          <div class="row">
            <x-adminlte-input label="Company" name="company" class="form-control" id="company" placeholder="Company"
              fgroup-class="col-md-6" />
            <x-adminlte-input label="Department" name="department" class="form-control" id="department"
              placeholder="Department" fgroup-class="col-md-6" />

          </div>
          <div class="row">
            <x-adminlte-input label="Jabatan" name="jabatan" class="form-control" id="jabatan" placeholder="Jabatan"
              fgroup-class="col-md-4" />
            <x-adminlte-input label="Lokasi" name="lokasi" class="form-control" id="lokasi" placeholder="Lokasi"
              fgroup-class="col-md-4" />
            <x-adminlte-input label="Bagian" name="bagian" class="form-control" id="bagian" placeholder="Bagian"
              fgroup-class="col-md-4" />
          </div>

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
      $('input[name="_token"]').val('{{ csrf_token() }}');
      $('#modalCreateUser').on('hide.bs.modal', function(e) {
        $('#modalCreateUser :input').val('').trigger('change');
        $('#modalCreateUser select').val('').trigger('change');
        $('input[name="_token"]').val('{{ csrf_token() }}');
        $('#formCreateUser [name="password"],#formCreateUser [name="password_conf"]').prop('required',
          true);
      });
      $('#modalCreateUser').on('submit', 'form', function(e) {
        e.preventDefault();
        $('#formCreateUser').removeClass('was-validated');
        $(`#modalCreateUser .invalid-feedback`).remove();
        $(`#modalCreateUser .is-invalid`).removeClass('is-invalid');
        const data = $(this).serializeArray();
        const tglLahir = data.find(x => x.name === 'tgl_lahir');
        const split = tglLahir?.value.split('/');
        const id = $('#formCreateUser [name="id"]').val();
        tglLahir.value = split ? `${split[2]}-${split[1]}-${split[0]}` : '';

        var formData = new FormData(this);
        if (id) {
          formData.append('_method', 'PUT');
        }
        formData.set('tgl_lahir', tglLahir.value);
        $.ajax({
          url: id ? "{{ url('user/') }}/" + id : "{{ route('user.store') }}", // if id exist use update URL
          method: 'POST', // if id exist use PUT
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          success: function(response) {
            toastr.success('User berhasil disimpan');
            @if ($gridId)
              window['{{ $gridId }}'].ajax.reload(null, false);
            @endif
            $('#modalCreateUser input, #modalCreateUser textarea').val('').trigger(
              'change');
            $('#modalCreateUser select').val('').trigger('change');
            $('input[name="_token"]').val('{{ csrf_token() }}');

            id && $('#modalCreateUser').modal('hide');
          },
          error: function(xhr) {
            if (xhr?.responseJSON?.message) {
              toastr.error(xhr?.responseJSON?.message);
              if (xhr?.responseJSON?.errors) {

                for (error in xhr.responseJSON.errors) {

                  const name = xhr.responseJSON.errors[error];
                  $(`#modalCreateUser [name="${error}"]`).addClass('is-invalid');
                  $(`#modalCreateUser [name="${error}"]`).parent().append(
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
