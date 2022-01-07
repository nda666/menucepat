<form id="settingSearchForm">
    <div class="row">
        <div class="col-md-6">
            <x-input-horizontal label="Kata Kunci" id="filterKey" name="filterKey" autocomplete="off"
                placeholder="Cari dari Kata Kunci" />
        </div>
        <div class="col-md-6">
            <x-input-horizontal label="Kata Kunci" id="filterValue" name="filterValue" autocomplete="off"
                placeholder="Cari dari Nilai" />

            <div class="form-group row">
                <div class="offset-md-3 col-md-9">
                    <button type="submit" class="btn btn-block btn-primary">Cari</button>
                </div>
            </div>
        </div>
        <hr>
</form>

@push('js')
<script>
    (function() {
            $('#settingSearchForm').submit(function(e) {
                e.preventDefault();
                @if ($onSubmit) {{ $onSubmit }} @endif
            })
        })()
</script>
@endpush