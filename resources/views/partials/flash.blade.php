@if(\Illuminate\Support\Facades\Session::has('flash_message'))
    <div class="alert alert-success {{Session::has('flash_message_important')?'alert-important':''}}">
        @if(\Illuminate\Support\Facades\Session::has('flash_message_important'))
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
        @endif
        {{\Illuminate\Support\Facades\Session::get('flash_message')}}
        {{Session('flash_message')}}
    </div>


@endif

@if (Session::has('flash_custom.message'))
        <div class="alert alert-{{ Session::get('flash_custom.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            {{ Session::get('flash_custom.message') }}
            {{ Session::set('flash_custom', array())}}
        </div>
@endif
