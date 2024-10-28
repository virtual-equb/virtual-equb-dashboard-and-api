@extends('layouts.app')

@section('styles')
<style type="text/css">
    .details-control {
        background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown .details-control {
        background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
    }
    .form-group.required .control-label:after {
        content: "*";
        color: red;
    }
    .table-responsive {
        overflow-x: auto;
    }
    @media (max-width: 768px) {
        .responsive-input {
            width: 100%;
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Role') }}</th>
                                                <th>{{ __('Permissions') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $role)
                                            <tr>
                                                <td>
                                                    <h4 class="text-capitalize fw-bold mb-0">{{ ucfirst($role->name) }}</h4>
                                                </td>

                                                @if($role->name == 'admin')
                                                <td>
                                                    <span class="badge bg-success">{{ __('Admin has all the permissions') }}</span>
                                                </td>
                                                <td>-</td> <!-- Display dash for actions -->
                                                @else
                                                <?php $permissions = $role->permissions; ?>
                                                @if(count($permissions) != 0)
                                                <td style="display: flex; flex-wrap: wrap;">
                                                    @foreach($permissions as $permission)
                                                    <span class="badge rounded p-2 m-1 px-3 bg-{{$permission->name=='access_all_data'?'success':'primary'}}">
                                                        {{ $role->hasPermissionTo($permission) ? str_replace("_", " ", $permission->name) : '' }}
                                                    </span>
                                                    @endforeach
                                                </td>
                                                @else
                                                <td class="align-items-center">
                                                    <span>
                                                        {{ __('No Permissions Assigned!') }}
                                                    </span>
                                                </td>
                                                @endif
                                                <td class="align-items-center">
                                                    <div class="d-flex">
                                                        <a href="/roles/edit/{{$role->id}}" class="card-link"><i class='bx bx-edit mx-1'></i></a>
                                                        <a href="javascript:void(0);" type="button" data-id="{{$role->id}}" data-type="roles" class="card-link mx-4 delete"><i class='bx bx-trash text-danger mx-1'></i></a>
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection