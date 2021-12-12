<div class="form-group row">
    <label class="{{ $labelClass ? $labelClass : " col-md-3 col-form-label text-md-right"}}">{{ $label }}</label>

    <div class="{{$fgroupClass ? $fgroupClass : " col-md-9"}}">
        <input class="form-control" {{ $attributes->merge(['class' => 'form-control']) }} />
    </div>
</div>