<div class="box-body">

    @include('common.show-errors')

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">@lang('common.email')</label>
                <input type="text" class="form-control" id="email" name="email" {{ $email_disabled ?? "" }} value="{{ $admin->email or old('email') }}" placeholder="@lang('common.email')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('common.name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $admin->name or old('name') }}" placeholder="@lang('common.name')">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">@lang('common.password')</label>
                <input type="password" class="form-control" id="password" name="password" value="" placeholder="@lang('common.password')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone_number">@lang('common.phone-number')</label>
                <div class="input-group date">
                    @if(app()->isLocale('en'))<div class="input-group-addon">962</div>@endif
                    <input style="direction: ltr" type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="ex: 791234567">
                    @if(app()->isLocale('ar'))<div class="input-group-addon">962</div>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="role">@lang('common.role')</label>
                <select class="form-control role" id="role" name="role">
                    @php
                        $role = $admin_role_id ?? old('role') ?? 0;
                    @endphp
                    <option value='0' {{ $role == 0 ? 'selected' : '' }}>@lang('role.select')</option>
                    <option value='1' {{ $role == 1 ? 'selected' : '' }}>@lang('role.super_admin')</option>
                    <option value='2' {{ $role == 2 ? 'selected' : '' }}>@lang('role.facility_manager')</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 facility-manager hidden">
            <div class="form-group">
                <label for="facilities">@lang('facility.multi-select')</label>
                <select style="width:100%" id="facilities" class="form-control js-example-basic-multiple" name="facilities[]" multiple="multiple">
                    @foreach(\App\Facility::orderBy('created_at', 'desc')->get() AS $facility)
                        <option value="{{ $facility->publicId() }}">{{ $facility->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div><!-- /.box-body -->

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }} ">
@endpush

@push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            let role = document.querySelector('.role');
            if (role.options[role.selectedIndex].value === '2') {
                document.querySelectorAll('.facility-manager').forEach(el => {
                    el.className = "col-md-6 facility-manager";
                });
            }
            $('.js-example-basic-multiple').select2({
                theme: 'classic'
            });
            role.addEventListener('change', () => {
                if (role.options[role.selectedIndex].value === '2') {
                    document.querySelectorAll('.facility-manager').forEach(el => {
                        el.className = "col-md-6 facility-manager";
                    });
                } else {
                    document.querySelectorAll('.facility-manager').forEach(el => {
                        el.className = "col-md-6 facility-manager hidden";
                });
            }
        })
        });
    </script>
@endpush
