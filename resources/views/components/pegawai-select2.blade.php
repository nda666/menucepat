@if ($fgroupClass)
  <div class="form-group row">
    <label
      class="{{ $labelClass ? $labelClass : ' col-md-3 col-form-label text-md-right' }}">{{ $label }}</label>

    <div class="{{ $fgroupClass ? $fgroupClass : ' col-md-9' }}">
      <select id="{{ $id }}" class="form-control" {{ $attributes->merge(['class' => 'form-control']) }}>
        {{ $slot }}
      </select>
    </div>
  </div>

@else
  <x-adminlte-select :name="$name" id="{{ $id }}" {{ $attributes->merge(['class' => 'form-control']) }}
    :label="$label" required autocomplete="off" />
@endif

@push('js')
  <script>
    (function() {
      let parent = $('#{{ $id }}').closest('.modal').length > 0 ? $('#{{ $id }}').closest(
        '.modal') : 'body';
      $('#{{ $id }}').select2({
        dropdownParent: parent,
        searchInputPlaceholder: 'Cari dari Nama atau NIK',
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
                  text: `${obj.nama} - ${obj.nik}`
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
    })()
  </script>
@endpush
