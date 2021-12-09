<form id="userSearchForm">
    <div class="row">
        <x-adminlte-input id="filterNik" name="filterNik" type="text" label="NIK" autocomplete="off"
            placeholder="Cari dari NIK" fgroup-class="col-md-4" />
        <x-adminlte-input id="filterNama" name="filterNama" type="text" label="Nama" autocomplete="off"
            placeholder="Cari dari nama" fgroup-class="col-md-4" />
        <x-adminlte-input id="filterEmail" name="filterEmail" type="text" label="Email" autocomplete="off"
            placeholder="Cari dari email" fgroup-class="col-md-4" />

    </div>
    <div class="row">
        <div class="offset-md-8 col-md-4">
            <button type="submit" class="btn btn-block btn-primary">Cari</button>
        </div>
    </div>
    <hr>
</form>

@push('js')
<script>
    (function(){
        $('#userSearchForm').submit(function(e){
            e.preventDefault();
            @if ($onSubmit) {{ $onSubmit }} @endif
        })
    })()
</script>
@endpush