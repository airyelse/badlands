<div {!! $attributes !!}>
    @if ($title || $tools)
        <div class="card-header {{ $divider ? 'with-border' : '' }}">
            <span class="card-box-title">{!! $title !!}</span>
            <div class="box-tools pull-right">
                @foreach($tools as $tool)
                    {!! $tool !!}
                @endforeach
            </div>
        </div>
    @endif
    <div class="card-body" style="{!! $padding !!}">
        <div id='formio'></div>
    </div>
    @if($footer)
        <div class="card-footer">
            {!! $footer !!}
        </div>
    @endif
</div>
<script>
    Dcat.ready(function () {
        Formio.createForm(document.getElementById('formio'), 'https://examples.form.io/example');
    });
</script>
