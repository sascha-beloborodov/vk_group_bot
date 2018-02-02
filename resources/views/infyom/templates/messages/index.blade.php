@extends('infyom.layouts.app')

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="container" id='app'>
                    <messages-list></messages-list>
                </div>
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection

