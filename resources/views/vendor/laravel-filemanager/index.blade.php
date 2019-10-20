<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Chrome, Firefox OS and Opera -->
  <meta name="theme-color" content="#75C7C3">
  <!-- Windows Phone -->
  <meta name="msapplication-navbutton-color" content="#75C7C3">
  <!-- iOS Safari -->
  <meta name="apple-mobile-web-app-status-bar-style" content="#75C7C3">

  <title>@lang('lfm.title-page')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('img/folder.png') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('css/lfm.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mfb.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
</head>
<body>
  <div class="container-fluid" id="wrapper">
    <div class="panel panel-primary hidden-xs">
      <div class="panel-heading">
        <h1 class="panel-title">@lang('lfm.title-panel')</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2 hidden-xs">
        <div id="tree"></div>
      </div>

      <div class="col-sm-10 col-xs-12" id="main">
        <div id="alerts"></div>
        <div id="content"></div>
      </div>

      <ul id="fab">
        <li><a href="#"></a>
          <ul class="hide">
            <li><a href="#" id="add-folder" data-mfb-label="@lang('lfm.nav-new')"><i class="fa fa-folder"></i></a></li>
            <li><a href="#" id="upload" data-mfb-label="@lang('lfm.nav-upload')"><i class="fa fa-upload"></i></a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">@lang('lfm.title-upload')</h4>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data' class="dropzone">
            <div class="form-group" id="attachment">

              <div class="controls text-center">
                <div class="input-group" style="width: 100%">
                  <a class="btn btn-primary" id="upload-button">@lang('lfm.message-choose')s</a>
                </div>
              </div>
            </div>
            <input type='hidden' name='working_dir' id='working_dir'>
            <input type='hidden' name='type' id='type' value='{{ request("type") }}'>
            <input type='hidden' name='_token' value='{{csrf_token()}}'>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lfm.btn-close')</button>
        </div>
      </div>
    </div>
  </div>

  <div id="lfm-loader">
    <img src="{{asset('img/loader.svg')}}">
  </div>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('js/jquery.form.min.js') }}"></script>
  <script src="{{ asset('js/dropzone.min.js') }}"></script>
  <script>
    var route_prefix = "{{ url('/') }}";
    var lfm_route = "{{ url(config('lfm.url_prefix', config('lfm.prefix'))) }}";
    var lang = {!! json_encode(__('lfm')) !!};
  </script>
  <script src="{{ asset('js/lfm-script.js') }}"></script>
  <script>
    $.fn.fab = function () {
      var menu = this;
      menu.addClass('mfb-component--br mfb-zoomin').attr('data-mfb-toggle', 'hover');
      var wrapper = menu.children('li');
      wrapper.addClass('mfb-component__wrap');
      var parent_button = wrapper.children('a');
      parent_button.addClass('mfb-component__button--main')
        .append($('<i>').addClass('mfb-component__main-icon--resting fa fa-plus'))
        .append($('<i>').addClass('mfb-component__main-icon--active fa fa-times'));
      var children_list = wrapper.children('ul');
      children_list.find('a').addClass('mfb-component__button--child');
      children_list.find('i').addClass('mfb-component__child-icon');
      children_list.addClass('mfb-component__list').removeClass('hide');
    };
    $('#fab').fab({
      buttons: [
        {
          icon: 'fa fa-folder',
          label: "@lang('lfm.nav-new')",
          attrs: {id: 'add-folder'}
        },
        {
          icon: 'fa fa-upload',
          label: "@lang('lfm.nav-upload')",
          attrs: {id: 'upload'}
        }
      ]
    });

    Dropzone.options.uploadForm = {
      paramName: "upload[]", // The name that will be used to transfer the file
      uploadMultiple: false,
      parallelUploads: 5,
      clickable: '#upload-button',
      dictDefaultMessage: '@lang('lfm.drop-upload')',
      init: function() {
        var _this = this; // For the closure
        this.on('success', function(file, response) {
          if (response == 'OK') {
            refreshFoldersAndItems('OK');
          } else {
            this.defaultOptions.error(file, response.join('\n'));
          }
      });
      },
      acceptedFiles: "{{ lcfirst(str_singular(request('type'))) == 'image' ? implode(',', config('lfm.valid_image_mimetypes')) : implode(',', config('lfm.valid_file_mimetypes')) }}",
      maxFilesize: ({{ lcfirst(str_singular(request('type'))) == 'image' ? config('lfm.max_image_size') : config('lfm.max_file_size') }} / 1000)
    }
  </script>
</body>
</html>
