@can('view permission')
@extends('layouts.app')
@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create Permissions
                                <a href="{{ url('permission')}}" class="btn btn-danger float-right"> Back</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('permission')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="">Permission Name</label>
                                    <input type="text" name="name" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@endcan
