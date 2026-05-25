@if ($crud->hasAccess('create'))
  <a href="{{ url($crud->route.'/create') }}" class="btn btn-primary">
    <i class="la la-plus"></i> Buat User Baru
  </a>
@endif
