<form id="announcementSearchForm">
    <div class="row">
        <x-adminlte-input id="filterTitle" name="filterTitle" type="text" label="Title" autocomplete="off"
            placeholder="Cari dari Title" fgroup-class="col-md-6" />


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
            $('#announcementSearchForm').submit(function(e) {
                e.preventDefault();
                @if ($onSubmit) {{ $onSubmit }} @endif
            })
        })()
    </script>
@endpush
