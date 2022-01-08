<form id="user-scheduleSearchForm">
  <div class="row">
    <div class="col-md-6">
      <x-input-horizontal label="Judul" id="filterTitle" name="filterTitle" autocomplete="off"
        placeholder="Cari dari Judul" />
      <x-input-horizontal label="Deskripsi" id="filterDescription" name="filterDescription" autocomplete="off"
        placeholder="Cari dari Deskripsi" />

    </div>
    <div class="col-md-6">
      <div class="form-group row">
        <label class=" col-md-3 col-form-label text-md-right">Tgl Mulai</label>
        <div class="col-md-9">
          <x-date-picker id="filterStartDate" placeholder="Cari dari Tanggal Mulai" />
        </div>
      </div>
      <div class="form-group row">
        <label class=" col-md-3 col-form-label text-md-right">Tgl Berakhir</label>
        <div class="col-md-9">
          <x-date-picker id="filterEndDate" placeholder="Cari dari Tanggal Berakhir" />
        </div>
      </div>


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
    (function() {
      $('#user-scheduleSearchForm').submit(function(e) {
        e.preventDefault();
        @if ($onSubmit) {{ $onSubmit }} @endif
      })
    })()
  </script>
@endpush
