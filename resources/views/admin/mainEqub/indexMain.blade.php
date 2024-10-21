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
                    <h1 class="m-0">{{'Equbs'}}</h1>
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
    {{-- <input type="hidden" id="lable" value="{{ $lables }}"> --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach ($equbs as $equb)
                    <div class="col-md-4">
                        <div class="">
                            <div class="card bg-info">
                                {{-- <img class="card-img-top" src="{{ asset('storage/' . $equb->image) }}" alt="Card image cap"> --}}
                                <div class="card-body">
                                    <h5 class="card-title">{{ $equb->name }}</h5>
                                    <p class="card-text">{{ $equb->remark }}.</p>
                                    <p class="card-text"><small class="text-sm text-white">Created Date {{ $equb->created_at }}</small></p>
                                    {{-- <a href="{{ route('viewMainEqub', $equb->id) }}" class="small-box-footer text-white mt-5">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a> --}}
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>


@endsection

@endif