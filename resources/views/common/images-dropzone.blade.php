@extends('layouts.app')

@section('content')
    <div class="box-body">
        <div class="row margin-bottom">
            <div class="col-md-12">
                <form id="imageHolder" action="{{ route($model.'.images.store', $id) }}" class="dropzone">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="model" value="{{ $model }}">
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-primary pull-right flip" href="{{URL::previous()}}">@lang('common.update-images')</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.options.imageHolder = {
            dictDefaultMessage: "Drag Images here or click (only 5 images)",
            dictRemoveFile: 'Remove',
            dictFileTooBig: 'Image is bigger than 2MB',
            dictCancelUpload: 'Cancel',
            acceptedFile: 'image/*',
            addRemoveLinks: true,
            paramName: "image",
            maxFilesize: 2,
            maxFiles: 5,
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                });

                this.on("success", function(file, response) {
                    file.serverId = response.serverId;
                });

                this.on("removedfile", function(file) {
                    $.ajax({
                        url: '/{{ $model }}/{{ $id }}/images',
                        type: "DELETE",
                        data: {
                            'server_id': file.serverId,
                            '_token': '{{ csrf_token() }}'
                        }
                    });
                });

                this.on("sending", function(file, xhr, formData) {
                    formData.append("filesize", file.size);
                });

                var myDropzone = this;
                $.get('/{{ $model }}/{{ $id }}/images', function(data) {
                    $.each(data.images, function (key, value) {
                        var file = {'name': value.name, 'size': value.size, 'serverId': value.name};
                        myDropzone.options.addedfile.call(myDropzone, file);
                        myDropzone.options.thumbnail.call(myDropzone, file, value.thumbnail);
                        myDropzone.emit('complete', file);
                    });
                });
            }
        };
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
<style>
    .dropzone .dz-preview .dz-image img {
        width: 100%;
    }
</style>
@endpush