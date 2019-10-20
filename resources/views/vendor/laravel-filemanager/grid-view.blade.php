@if((sizeof($files) > 0) || (sizeof($directories) > 0))

<div class="row">

  @foreach($items as $item)
  <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 img-row">
    <?php $item_name = $item->name; ?>
    <?php $thumb_src = $item->thumb; ?>
    <?php $item_path = $item->is_file ? $item->url : $item->path; ?>

      {{--square--}}
    <div class="square {{ $item->is_file ? 'file' : 'folder'}}-item clickable" data-id="{{ $item_path }}" >
      @if($thumb_src)
      <img src="{{ $thumb_src }}">
      @else
      <i class="fa {{ $item->icon }} fa-5x"></i>
      @endif
    </div>

    <div class="caption text-center">
      <div class="btn-group">
        <button type="button" data-id="{{ $item_path }}"
                class="item_name btn btn-default btn-xs {{ $item->is_file ? 'file' : 'folder'}}-item" >
          {{ $item_name }}
        </button>
      </div>
    </div>

  </div>
  @endforeach

</div>

@else
<p>{{ Lang::get('laravel-filemanager::lfm.message-empty') }}</p>
@endif
