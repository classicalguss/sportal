@if (count($errors) > 0)
    <div class="alert alert-danger">
        @lang('common.errors')<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif