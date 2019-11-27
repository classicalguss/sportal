@include('common.noshow-modal', ['alert' => __('reservation.confirm-noshow')])
@include('common.delete-modal', ['alert' => __('reservation.confirm-delete')])
@include('common.show-message')

<div class="box-body table-responsive no-padding">
    <table class="table table-hover">
        <tbody><tr>
            <th>@lang('common.name')</th>
            <th>@lang('common.phone-number')</th>
            <th>@lang('common.facility')</th>
            <th>@lang('common.venue')</th>
            <th>@lang('common.date')</th>
            <th>@lang('common.time')</th>
            <th>@lang('common.status')</th>
            <th>@lang('common.actions')</th>
        </tr>
        @forelse($reservations AS $reservation)
            <tr>
                <td>{{ $reservation->customer->name ?? '' }}</td>
                <td>{{ $reservation->customer->phone_number ?? '' }}</td>
                <td>{{ $reservation->facility()->name() }}</td>
                <td>{{ $reservation->venue->name() }}</td>
                <td>{!! $reservation->date() !!}</td>
                <td>{!! $reservation->time() !!}</td>
                <td>
                    @if($reservation->status == \App\Reservation::RESERVATIONSTATUS_PENDING)
                        <span class="label label-default">@lang('reservation.status-pending')</span>
                    @elseif($reservation->status == \App\Reservation::RESERVATIONSTATUS_APPROVED)
                        <span class="label label-success">@lang('reservation.status-approved')</span>
                    @elseif($reservation->status == \App\Reservation::RESERVATIONSTATUS_HISTORY)
                        <span class="label label-primary">@lang('reservation.status-history')</span>
                    @elseif($reservation->status == \App\Reservation::RESERVATIONSTATUS_CANCELED)
                        <span class="label label-danger">@lang('reservation.status-canceled')</span>
                    @elseif($reservation->status == \App\Reservation::RESERVATIONSTATUS_NO_SHOW)
                        <span class="label label-warning">@lang('reservation.status-no_show')</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('reservations.show', $reservation->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                    @php $disabled = $reservation->status == \App\Reservation::RESERVATIONSTATUS_PENDING || $reservation->status == \App\Reservation::RESERVATIONSTATUS_APPROVED ? "" : "disabled"; @endphp
                    <form role="form" style="display: inline" method="POST" action="{{route('reservations.destroy', $reservation->publicId())}}" id="{{$reservation->publicId()}}" onsubmit="this.preventDefault()">
                        <input type="hidden" value="DELETE" name="_method">
                        {{ csrf_field() }}
                        <button {{ $disabled }} data-toggle="modal" title="@lang('reservation.status-canceled')" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$reservation->publicId()}}')"><i class="fa fa-trash-o"></i></button>
                    </form>
                    <form role="form" style="display: inline" method="POST" action="{{route('reservations.noShow', $reservation->publicId())}}" id="noshow-{{$reservation->publicId()}}" onsubmit="this.preventDefault()">
                        <input type="hidden" value="PATCH" name="_method">
                        {{ csrf_field() }}
                        <button {{ $disabled }} data-toggle="modal" title="@lang('reservation.status-no_show')" data-target="#noshowModal" type="button" class="btn btn-warning btn-sm" onclick="setNoShowFormId('{{$reservation->publicId()}}')"><i class="fa fa-times"></i></button>
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
