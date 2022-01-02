<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateFamily">
    Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateFamily" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">

        <form id="formCreateFamily">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Family</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                    {{ csrf_field() }}
                    <input type="hidden" name="id" autocomplete="off">
                    <x-adminlte-select id="select-user_id" name="user_id" label="User" required autocomplete="off"
                        placeholder="User" />
                    <x-adminlte-input name="nama" type="text" label="Nama" required autocomplete="off"
                        placeholder="Nama" />
                    <x-adminlte-select name="sex" required autocomplete="off" label="Gender">
                        <option disabled value="" selected>Pilih Gender</option>
                        @foreach ($sexType as $v)
                        <option value="{{ $v->value }}">{{ $v->description }}</option>
                        @endforeach

                    </x-adminlte-select>
                    <x-adminlte-input name="hubungan" label="Hubungan" required autocomplete="off"
                        placeholder="Latitude" />
                    <x-adminlte-input name="tempat_lahir" label="Tempat Lahir" required autocomplete="off"
                        placeholder="Longtitude" />

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
            $("#select-user_id").select2({
                dropdownParent: $("#modalCreateFamily"),
                width: '100%',
                ajax: {
                    url: "{{ route('user.select2') }}",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;

                        return {
                            results: $.map(data.data, function(obj) {
                                console.log(obj)
                                return {
                                    id: obj.id,
                                    text: obj.nama
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Pilih Pegawai',
            });

            $('input[name="_token"]').val('{{ csrf_token() }}');
            $('#modalCreateFamily').on('hide.bs.modal', function() {
                $('#modalCreateFamily :input').val('').trigger('change');
                $('#modalCreateFamily select').val('').trigger('change');
                $('#modalCreateFamily input[name="_token"]').val('{{ csrf_token() }}').trigger('change');
            });
            $('#modalCreateFamily').on('submit', 'form', function(e) {
                e.preventDefault();
                $('#formCreateFamily').removeClass('was-validated');
                $(`#formCreateFamily .invalid-feedback`).remove();
                $(`#formCreateFamily .is-invalid`).removeClass('is-invalid');
                const data = $(this).serializeArray();
                const id = $('#formCreateFamily [name="id"]').val();

                $.ajax({
                    url: id ? "{{ url('family/') }}/" + id :
                    "{{ route('family.store') }}", // if id exist use update URL
                    method: id ? 'PUT' : 'POST', // if id exist use PUT
                    data,
                    success: function(response) {
                        toastr.success('Family berhasil disimpan');
                        @if ($gridId)
                            window['{{ $gridId }}'].ajax.reload(null, false);
                        @endif
                        $('#modalCreateFamily input, #modalCreateFamily textarea').val('')
                            .trigger(
                                'change');
                        $('#modalCreateFamily select').val('').trigger('change');
                        $('input[name="_token"]').val('{{ csrf_token() }}');

                        id && $('#modalCreateFamily').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr?.responseJSON?.message) {
                            toastr.error(xhr?.responseJSON?.message);
                            if (xhr?.responseJSON?.errors) {
                                for (error in xhr.responseJSON.errors) {

                                    const name = xhr.responseJSON.errors[error];
                                    $(`#modalCreateFamily [name="${error}"]`).addClass(
                                        'is-invalid')
                                    $(`#modalCreateFamily [name="${error}"]`).parent().append(
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