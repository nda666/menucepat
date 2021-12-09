<form id="locationSearchForm">
    <div class="row">
        <x-adminlte-input id="filterNama" name="filterNama" type="text" label="Nama" autocomplete="off"
            placeholder="Cari dari Nama" fgroup-class="col-md-6" />


    </div>
    <div class="row">
        <div class="col-md-6">
            <button type="submit" class="btn btn-block btn-primary">Cari</button>
        </div>
    </div>
    <hr>
</form>

@push('js')
    <script>
        (function() {
            $('#locationSearchForm').submit(function(e) {
                e.preventDefault();
                @if ($onSubmit) {{ $onSubmit }} @endif
            })
        })()
    </script>
@endpush
