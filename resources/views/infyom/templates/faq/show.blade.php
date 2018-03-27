@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            F A Q
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('templates.f_a_q_s.show_fields')
                    <a href="{!! route('fAQS.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
