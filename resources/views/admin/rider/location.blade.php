@extends('admin.layouts.master')

@section('css')

@endsection

@section('content')
<section class="content">
    <div class="col-md-12">
        <div class="text-center"><h5>Rider <b>"{{ $rider->rider_user_name }}'s"</b> Current Location</h5></div>
        <div class="row mt-2 mb-2" style="border-style:solid">
            <iframe width="100%"
                height="450px"
                frameborder="0"
                scrolling="no"
                marginheight="0"
                marginwidth="0"
                src = "https://maps.google.com/maps?q={{ $rider->rider_latitude }},{{ $rider->rider_longitude }}&hl=es;z=14&amp;output=embed">
            </iframe>
        </div>
        <a href="{{url('fatty/main/admin/riders')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>Rider lists</span></a>
    </div>
</section>
@endsection
@push('scripts')
@endpush
