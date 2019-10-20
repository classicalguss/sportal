@include('venue.create')
@include('venue.create-multi')
@include('common.delete-modal')

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <div class="box-title" style='left:10px'>
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control input-sm" placeholder="@lang('common.name')" value="{{ request('name') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                        </div>
                    </form>
                </div>
                @can(\App\Permission::PERMISSION_CREATE_VENUES)
                <div class="box-tools">
                    <button class="btn btn-warning btn-bg" data-toggle="modal" data-target="#create-multi-venue">@lang('venue.create-multi')</button>
                    <button class="btn btn-success btn-bg" data-toggle="modal" data-target="#create-venue">@lang('venue.create')</button>
                </div>
                @endcan
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody><tr>
                        <th>@lang('common.venue')</th>
                        @if(isset($show_facility) AND $show_facility == true)
                            <th>@lang('common.facility')</th>
                        @endif
                        <th>@lang('common.city')</th>
                        <th>@lang('common.marker')</th>
                        <th>@lang('common.types')</th>
                        <th>@lang('common.venue-kind')</th>
                        <th>@lang('common.indoor')</th>
                        <th>@lang('common.actions')</th>
                    </tr>
                    @forelse($venues AS $venue)
                        <tr>
                            <td>{{ $venue->name() }}</td>
                            @if(isset($show_facility) AND $show_facility == true)
                                <td>{{ $venue->facilityName() }}</td>
                            @endif
                            <td>{{ $venue->cityName() }}</td>
                            <td>{{ $venue->markerName() }}</td>
                            <td>{!! $venue->typesName() !!}</td>
                            <td>{!! $venue->kindName() !!}</td>
                            <td>
                                @if($venue->indoor == 1)
                                    <span class="label label-success">@lang('venue.indoor')</span>
                                @elseif($venue->indoor == 0)
                                    <span class="label label-primary">@lang('venue.outdoor')</span>
                                @endif
                            </td>
                            <td>
                                <form role="form" method="POST" action="{{route('venues.destroy', $venue->publicId())}}" id="{{$venue->publicId()}}" onsubmit="this.preventDefault()" >
                                    <input type="hidden" value="DELETE" name="_method">
                                    {{ csrf_field() }}
                                    <div class="btn-group">
                                        <a href="{{ route('venues.show', $venue->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                        <a href="{{ route('venues.edit', $venue->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        @can(\App\Permission::PERMISSION_UPDATE_VENUE_AVAILABILITIES)
                                        <button data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$venue->publicId()}}')"><i class="fa fa-trash-o"></i></button>
                                        @endcan
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">@lang('common.no-results')</td>
                        </tr>
                    @endforelse
                    </tbody></table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
        {{ $venues->appends(['name' => request('name')])->links() }}
    </div>
</div>