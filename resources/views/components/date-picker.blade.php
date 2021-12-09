<div id="date-container-{{$id}}" class="
    @if (isset($attributes['label'])) form-group @endif
    @if (isset($attributes['fgroup-class'])) {{$attributes['fgroup-class']}} @endif
    ">
    @if (isset($attributes['label']))
    <label for="{{ $id }}">{{ $attributes['label'] }}</label>
    @endif
    <input type="text" {{ $attributes->merge(['class' => 'form-control']) }} id="{{ $id }}">
</div>
@push('js')
<script>
    (function() {
        var elem = document.querySelector('input[id="{{$id}}"]');
         window.{{$id}} = new Datepicker(elem, {
            format: 'dd/mm/yyyy',
        buttonClass: 'btn',
        container: 'div[id="date-container-{{$id}}"]'
      });
    })();
</script>
@endpush
