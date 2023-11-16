@if($label)
    <div class="form-group row form-field">
        <div class="{{$viewClass['label']}}"><h6 class="pull-right">{!! $label !!} </h6></div>
        <div class="{{$viewClass['field']}}">
            <div id="embed-{{$column}}" class="embed-{{$column}}">
                <div class="embed-{{$column}}-forms">
                    <div class="embed-{{$column}}-form fields-group">
                        @foreach($form->fields() as $field)
                            {!! $field->render() !!}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
