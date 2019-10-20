@extends('layouts.app')

@section('content')

<section class="invoice">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center">{{ $facility->name() }}</h2>

            <div class="table-responsive margin-bottom">
                <table class="table">
                    <tbody><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.city')</th>
                        <td class="text-left flip">{{ $facility->cityName() }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.marker')</th>
                        <td class="text-left flip">{{ $facility->markerName() }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.address')</th>
                        <td class="text-left flip">{{ $facility->addressName() }}</td>
                    </tr></tbody>
                </table>
            </div>

            <div class="row margin-bottom">
            <div class="text-center">
                <a href="{{ route('facilities.edit', $facility->publicId()) }}" type="button" class="btn btn-primary" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i> @lang('facility.update')
                </a>
            </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row margin-bottom">
                <div class="col-md-offset-3 col-md-6">
                    @include('common.images-slider')
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <a href="{{ route('facilities.images.edit', $facility->publicId()) }}" type="button" class="btn btn-primary" style="margin-right: 5px;">
                        <i class="fa fa-edit"></i> @lang('common.update-images')
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include('common.show-errors')
@include('common.show-message')

@include('venue.include.index')

@endsection