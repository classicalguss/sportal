@extends('layouts.app')

@section('content')
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <a id="addImage" class="btn btn-primary pull-left flip">
                    <i class="fa fa-picture-o"></i> @lang('common.choose-image')
                </a>
                <a id="addImage" class="btn btn-primary pull-right flip" href="{{URL::previous()}}">
                    <i class="fa fa-arrow-left"></i> @lang('common.back')
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form id="imageHolder" method="POST" action="{{ route($model.'.update.images', $model_id) }}">
                    <div class="">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        <input type="hidden" id="urls" name="urls" value="null">
                        <div class="row" style="margin: 15px; padding: 15px; border: 2px solid gray; min-height: 237px" id="imgs">
                            @foreach($images AS $image)
                                <div class="col-sm-2" style="margin-bottom:10px;">
                                    <img id="hold" class="img-responsive" src="{{ $image->thumbnail }}" filename="{{ $image->filename }}">
                                    <a class="btn btn-lg btn-danger" style="display:none;"><i class="fa fa-trash"></i></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right flip">@lang('common.update-images')</button>
                </form>
            </div>
        </div>
    </div><!-- /.box-body -->
@endsection

@push('scripts')

    <script>
        (function( $ ){
            $.fn.filemanager = function(type, options) {
                type = type || 'file';
                this.on('click', function(e) {
                    var route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
                    localStorage.setItem('target_input', $(this).data('input'));
                    localStorage.setItem('target_preview', $(this).data('preview'));
                    window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
                    window.SetUrl = function (_url, file_path) {
                        //set the value of the desired input to image url
                        var target_input = $('#' + localStorage.getItem('target_input'));
                        target_input.val(file_path).trigger('change');

                        //set or change the preview image src
                        var target_preview = $('#' + localStorage.getItem('target_preview'));
                        let thumb_url = _url.substr(0, _url.lastIndexOf('/')) + '/thumbs/' + _url.substr(_url.lastIndexOf('/')+1);
                        $.ajax({
                            type: 'HEAD',
                            url: thumb_url,
                            success: function () {
                                target_preview.attr('src', thumb_url).trigger('change');
                                target_preview[0].setAttribute('filename', _url);
                            },
                            error: function() {
                                target_preview.attr('src', _url).trigger('change');
                                target_preview[0].setAttribute('filename', _url);
                            }
                        });


                    };
                    return false;
                });
            }
        })(jQuery);
    </script>

    <script>
        (function() {
            window.addEventListener('load',function () {
                let cols = document.querySelector('#imgs');
                for (let i = 0; i < cols.childNodes.length; i ++) {
                    if (cols.childNodes[i].childNodes[1] && cols.childNodes[i].childNodes[3]) {
                        addDeleteHover(cols.childNodes[i].childNodes[1], cols.childNodes[i].childNodes[3], cols.childNodes[i]);
                        imgReferences.push(cols.childNodes[i].childNodes[1]);
                    }
                }
            });


            let locale = '{{ app()->getLocale() }}';
            let holder = 1;
            let imgReferences = [];
            document.querySelector('#addImage').addEventListener('click', addImageUpload);
            document.querySelector('#imageHolder').addEventListener('submit', sendUrlList);
            function sendUrlList(e) {
                let urls = [];
                for (let i = 0; i < imgReferences.length; i ++) {
                    urls.push({
                        filename: imgReferences[i].getAttribute("filename"),
                        thumbnail: imgReferences[i].currentSrc
                    });
                }

                document.querySelector('#urls').value = JSON.stringify(urls);
            }
            function createElement(type, options, attributes, children) {
                let el = document.createElement(type);
                if (options)
                    for (let key in options) {
                        el[key] = options[key];
                    }
                if (attributes)
                    for (let key in attributes) {
                        el.setAttribute(key,attributes[key]);
                    }
                if (children) {
                    for (let i = 0; i < children.length; i++) {
                        el.appendChild(children[i]);
                    }
                }
                return el;
            }
            function text(str) {
                return document.createTextNode(str);
            }
            function addDeleteHover(img, del_btn, col) {
                function over() {
                    del_btn.style.position = 'absolute';
                    del_btn.style.top = '0px';
                    if(locale == 'ar')
                        del_btn.style.right = '15px';
                    else
                        del_btn.style.left = '15px';
                    del_btn.style.display = 'inline-block';
                }
                function none() {
                    del_btn.style.display = 'none';
                }
                del_btn.addEventListener('mouseover', over);
                del_btn.addEventListener('mouseout', none);

                img.addEventListener('mouseover', over);
                img.addEventListener('mouseout', none);

                del_btn.addEventListener('click', function () {
                    col.remove();
                    imgReferences.splice(imgReferences.indexOf(img), 1);
                });
            }
            function addImageUpload() {
                if (imgReferences.length > 0 && !imgReferences[imgReferences.length-1].currentSrc) {
                    imgReferences[imgReferences.length-1].parentNode.remove();
                    imgReferences.splice(imgReferences.length-1 ,1);
                }

                let a = createElement('a', undefined, {
                    'id': 'lfm'+holder,
                    'data-input': "thumbnail",
                    'data-preview': "holder"+holder,
                    "style": "display:none;"
                });
                let img = createElement('img', undefined, {"id": "holder"+holder, "class": "img-responsive"});
                let idel = createElement('i', {className: 'fa fa-trash'});
                let del_btn = createElement('a', {className: 'btn btn-lg btn-danger'}, {"style": "display:none;"}, [idel]);

                let col= createElement(
                    'div',
                    {className: 'col-sm-2'},
                    {"style": "margin-bottom:10px;"},
                    [a, img, del_btn]
                );
                addDeleteHover(img, del_btn, col);

                let imgs = document.querySelector("#imgs");
                imgs.appendChild(col);
                imgReferences.push(img);
                $(`#lfm${holder}`).filemanager('image');
                holder ++;
                a.click();
                a.remove();
            }
        })()
    </script>

@endpush