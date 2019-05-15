@extends('layouts.app')

@section('content')

    {{ Breadcrumbs::render('leads-archive') }}


    <div id="leads-archive" class="container">

        <leads-archive :data-leads="{{ json_encode($leads) }}"></leads-archive>
    </div>
@endsection
