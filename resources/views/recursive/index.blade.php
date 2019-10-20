@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
    @include('common.show-message')

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        @lang('recursive.title')
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.venue')</th>
                            <th>@lang('common.date-from')</th>
                            <th>@lang('common.date-to')</th>
                            <th>@lang('common.time')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($recursives AS $recursive)
                        <tr>
                            <td>{{ $recursive->customer->name }}</td>
                            <td>{{ $recursive->venue->name() }}</td>
                            <td>{{ $recursive->date_start }}</td>
                            <td>{{ $recursive->date_finish }}</td>
                            <td>{!! $recursive->time() !!}</td>
                            <td><span class="label label-{{ \App\Recursive::$status_color[$recursive->status] }}">{{ \App\Recursive::$status[$recursive->status] }}</span></td>
                            <td>
                                @php $disabled = $recursive->status == \App\Recursive::RECURSIVESTATUS_ACTIVE ? "" : "disabled"; @endphp
                                <form role="form" method="POST" action="{{route('recursive.destroy', $recursive->publicId())}}" id="{{$recursive->publicId()}}" onsubmit="this.preventDefault()">
                                    <input type="hidden" value="DELETE" name="_method">
                                    {{ csrf_field() }}
                                    <div class="btn-group">
                                        <a href="{{ route('recursive.show', $recursive->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                        <button {{ $disabled }} data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$recursive->publicId()}}')"><i class="fa fa-stop"></i></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody></table>
                </div>
            </div>
            {{ $recursives->links() }}
        </div>
    </div>
@endsection
