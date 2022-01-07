<div class="form-group row">
    <label class="{{ $labelClass ? $labelClass : " col-md-3 col-form-label text-md-right"}}">{{ $label }}</label>

    <div class="{{$fgroupClass ? $fgroupClass : " col-md-9"}}">
        <select id="{{$id}}" class="form-control" {{ $attributes->merge(['class' => 'form-control']) }}>
            {{ $slot }}
        </select>
    </div>
</div>

@section('plugins.Select2', true)
@push('js')
<script>
    (function(){
        $('#{{$id}}').select2({
            width: '100%'
        });
    })()
</script>
@endpush