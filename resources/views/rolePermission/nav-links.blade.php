<div class="container mt-1 float-right">
    @can('view role')
    <a href="{{ url('roles') }}" class="btn btn-primary mx-2">Roles</a>
    @endcan
    @can('view permission')
    <a href="{{ url('permission') }}" class="btn btn-info mx-2">Permissions</a>
    @endcan
</div>