@if(Auth::user()->role == 'admin' ||
Auth::user()->role == 'general_manager' ||
Auth::user()->role == 'operation_manager' ||
Auth::user()->role == 'finance' ||
Auth::user()->role == 'assistant' ||
Auth::user()->role == 'it')


@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $mainEqub->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrump float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $mainEqub->sub_equb_count}}</h3>
                            <p>Total Sub Equbs</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{-- <a href="{{ route('showMember') }}" class="small-box-footer">More info 
                            <i class="fas fa-arrow-circle-right"></i>
                        </a> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $activeEqubs }}</h3>
                            <p>Active Sub Equbs</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        {{-- <a href="{{ route('showMember') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $deactiveEqubs }}</h3>
                            <p>Deactive Sub Equbs</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        {{-- <a href="#" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


@endsection

@endif