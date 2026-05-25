{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> Dashboard</a></li>
<x-backpack::menu-item title="Bimbingan" icon="la la-comments" :link="backpack_url('bimbingan')" />
<x-backpack::menu-item title="Laporan Mingguan" icon="la la-calendar" :link="backpack_url('laporan-mingguan')" />
<x-backpack::menu-item title="User" icon="la la-user" :link="backpack_url('user')" />

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">
        <i class="la la-file nav-icon"></i> Laporan
    </a>
    <div class="dropdown-menu">
        <x-backpack::menu-item title="Laporan" icon="la la-file-alt" :link="backpack_url('laporan')" />
    </div>
</li>