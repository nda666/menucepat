<form id="userSearchForm">
    <div class="row">
        <div class="col-md-6">
            <x-input-horizontal label="Nama" id="filterNama" name="filterNama" autocomplete="off"
                placeholder="Cari dari Nama" />
            <x-input-horizontal label="NIK" id="filterNik" name="filterNik" autocomplete="off"
                placeholder="Cari dari NIK" />
            <x-input-horizontal label="Email" id="filterEmail" name="filterEmail" autocomplete="off"
                placeholder="Cari dari Email" />
            <x-input-horizontal label="Device ID" id="filterDeviceId" name="filterDeviceId" autocomplete="off"
                placeholder="Cari dari Device ID" />
            <x-input-horizontal label="Divisi" id="filterDivisi" name="filterDivisi" autocomplete="off"
                placeholder="Cari dari Divisi" />

        </div>
        <div class="col-md-6">
            <x-input-horizontal label="Company" id="filterCompany" name="filterCompany" autocomplete="off"
                placeholder="Cari dari Company" />
            <x-input-horizontal label="Sub Divisi" id="filterSubDivisi" name="filterSubDivisi" autocomplete="off"
                placeholder="Cari dari Sub Divisi" />
            <x-input-horizontal label="Jabatan" id="filterJabatan" name="filterJabatan" autocomplete="off"
                placeholder="Cari dari Jabatan" />
            <x-select-horizontal label="Status" id="filterEmail" name="filterEmail" autocomplete="off"
                placeholder="Cari dari Email">
                <option value="">Tampilkan Semua</option>
                <option value="0">Terkunci</option>
                <option value="1">Tidak Terkunci</option>
            </x-select-horizontal>

            <div class="form-group row">
                <div class="offset-md-3 col-md-9">
                    <button type="submit" class="btn btn-block btn-primary">Cari</button>
                </div>
            </div>
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