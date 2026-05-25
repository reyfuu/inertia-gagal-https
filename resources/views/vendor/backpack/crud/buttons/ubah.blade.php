@if ($crud->hasAccess('update', $entry))
  <a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}" class="btn btn-sm btn-link text-primary text-decoration-none p-0">
    <i class="la la-edit"></i> Ubah
  </a>
@endif
