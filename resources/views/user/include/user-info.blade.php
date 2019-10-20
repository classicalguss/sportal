<section class="invoice">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center">{{ $user->name }}</h2>

            <div class="table-responsive margin-bottom">
                <table class="table">
                    <tbody><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.email')</th>
                        <td class="text-left flip">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.phone-number')</th>
                        <td class="text-left flip">{{ $user->phone_number }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.status')</th>
                        <td class="text-left flip"><span class="label label-{{ $user->userStatusColor() }}">{{ $user->userStatus() }}</span></td>
                    </tr>
                    @if($user->birth_date)
                    <tr>
                        <th class="text-right flip">@lang('common.birthday')</th>
                        <td class="text-left flip">{{ $user->birth_date }}</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row margin-bottom">
                <div class="col-md-offset-3 col-md-6">
                    @php $user_image = $user->image()->first() ? $user->image()->first()->filename : asset('img/no-image.png') @endphp
                    <img class="img-responsive center-block" src="{{ $user_image }}">
                </div>
            </div>
        </div>
    </div>
</section>
