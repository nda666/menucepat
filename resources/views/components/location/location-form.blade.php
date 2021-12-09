<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateLocation">
    Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateLocation" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">

        <form id="formCreateLocation">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                    {{ csrf_field() }}
                    <input type="hidden" name="id" autocomplete="off">
                    <x-adminlte-input name="nama" type="text" label="Nama" required autocomplete="off"
                        placeholder="Nama" />
                    <x-adminlte-input name="latitude" type="latitude" label="Latitude" required autocomplete="off"
                        placeholder="Latitude" />
                    <x-adminlte-input name="longtitude" type="longtitude" label="Longtitude" required autocomplete="off"
                        placeholder="Longtitude" />
                    <x-adminlte-input name="radius" type="radius" label="Radius" required autocomplete="off"
                        placeholder="Radius" />
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
            $('#modalCreateLocation').on('hide.bs.modal', function(e) {
                $('#modalCreateLocation input, #modalCreateLocation textarea').val('').trigger('change');
                $('#modalCreateLocation select').val('').trigger('change');
                $('input[name="_token"]').val('{{ csrf_token() }}');
            });
            $('#modalCreateLocation').on('submit', 'form', function(e) {
                e.preventDefault();
                $('#formCreateLocation').removeClass('was-validated')
                $(`#formCreateLocation .invalid-feedback`).remove();
                $(`#formCreateLocation .is-invalid`).removeClass('is-invalid');
                const data = $(this).serializeArray();
                const id = $('#formCreateLocation [name="id"]').val();

                $.ajax({
                    url: id ? "{{ url('location/') }}/" + id :
                    "{{ route('location.store') }}", // if id exist use update URL
                    method: id ? 'PUT' : 'POST', // if id exist use PUT
                    data,
                    success: function(response) {
                        toastr.success('Location berhasil disimpan');
                        @if ($gridId)
                            window['{{ $gridId }}'].ajax.reload(null, false);
                        @endif
                        $('#modalCreateLocation input, #modalCreateLocation textarea').val('')
                            .trigger(
                                'change');
                        $('#modalCreateLocation select').val('').trigger('change');
                        $('input[name="_token"]').val('{{ csrf_token() }}');

                        id && $('#modalCreateLocation').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr?.responseJSON?.message) {
                            toastr.error(xhr?.responseJSON?.message);
                            if (xhr?.responseJSON?.errors) {
                                for (error in xhr.responseJSON.errors) {

                                    const name = xhr.responseJSON.errors[error];
                                    $(`#modalCreateLocation [name="${error}"]`).addClass(
                                        'is-invalid')
                                    $(`#modalCreateLocation [name="${error}"]`).parent().append(
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
