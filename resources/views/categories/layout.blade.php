@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- <nav class="col-md-2 d-none d-md-block bg-light sidebar py-4">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('categories.index') }}">
                            Categories
                        </a>
                    </li>
                    <!-- Add more menu items here -->
                </ul>
            </div>
        </nav> --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @yield('category-content')
        </main>
    </div>
</div>
@endsection
