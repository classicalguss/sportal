<div class="row">
    <div class="col-xs-12">
        <div class="">
            <div class="table-responsive">
                <table class="table">
                    <tbody><tr>
                        <th>@lang('common.date')</th>
                        <th>@lang('common.status')</th>
                        <th>@lang('common.actions')</th>
                    </tr>
                    @forelse($reservations_availability AS $date => $reservation)
                        @php $status = $reservation['status'] @endphp
                        <tr class="{{ \App\VenueAvailability::$status_color[$status] }}">
                            <td>{{ $date }}</td>
                            <td>{{ \App\VenueAvailability::$status[$status] }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">@lang('common.no-results')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>