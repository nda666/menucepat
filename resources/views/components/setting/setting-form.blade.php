<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateSetting">
    Tambah
</button>

<!-- Modal -->
<div class="modal fade" id="modalCreateSetting" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">

        <form id="formCreateSetting">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" autocomplete="off">

                    <x-adminlte-input name="key" label="Kata Kunci" required autocomplete="off"
                        placeholder="Kata Kunci" />
                    <x-adminlte-textarea name="value" label="Nilai" required autocomplete="off" placeholder="Nilai" />



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
            $('#modalCreateSetting').on('hide.bs.modal', function() {
                $('#modalCreateSetting :input').val('').trigger('change');
                $('#modalCreateSetting select').val('').trigger('change');
                $('#modalCreateSetting input[name="_token"]').val('{{ csrf_token() }}').trigger(
                    'change');
            });
            $('#modalCreateSetting').on('submit', 'form', function(e) {
                e.preventDefault();
                $('#formCreateSetting').removeClass('was-validated');
                $(`#formCreateSetting .invalid-feedback`).remove();
                $(`#formCreateSetting .is-invalid`).removeClass('is-invalid');
                const data = $(this).serializeArray();
                const id = $('#formCreateSetting [name="id"]').val();
                const form = new FormData($(this)[0]);
              
                $.ajax({
                    url: id ? "{{ url('setting/') }}/" + id :
                    "{{ route('setting.store') }}", // if id exist use update URL
                    method: 'POST', // if id exist use PUT
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success('Setting berhasil disimpan');
                        @if ($gridId)
                            window['{{ $gridId }}'].ajax.reload(null, false);
                        @endif
                        $('#modalCreateSetting input, #modalCreateSetting textarea')
                            .val('').trigger('change');
                        $('#modalCreateSetting select').val('').trigger('change');
                        $('input[name="_token"]').val('{{ csrf_token() }}');

                        id && $('#modalCreateSetting').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr?.responseJSON?.message) {
                            toastr.error(xhr?.responseJSON?.message);
                            if (xhr?.responseJSON?.errors) {
                                for (error in xhr.responseJSON.errors) {

                                    const name = xhr.responseJSON.errors[error];
                                    $(`#modalCreateSetting [name="${error}"]`).addClass(
                                        'is-invalid')
                                    $(`#modalCreateSetting [name="${error}"]`).parent().append(
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