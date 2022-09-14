<div class="{{$class}}">
    @if($user->status)
        <h4>
            [Active]
        </h4>
    @else
        <h4>
            [Inactive]
        </h4>
    @endif
    <h6>
        <b>
            {{$user->email}}
        </b>
    </h6>
    <h6>
        {{$user->name}}
    </h6>
    <h6>
        gender: {{$user->gender}}
    </h6>
</div>
