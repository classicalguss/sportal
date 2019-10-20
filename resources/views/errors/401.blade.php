@extends('layouts.app')

@section('content')
    <div class="error-page">
        <h2 class="headline text-yellow">401</h2>

        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Unauthorized.</h3>

            <p>
                You are not Authorized to view this page.
            </p>

        </div>
    </div>
    <!-- /.error-page -->
@endsection