@extends('layouts.app')

@section('content')
    <main class="grid grid--w950 spacer__top--big">
        <h1 class="page--title">Contact</h1>

        <section class="grid--flex flex--column">
            <div class="page-content">
                @yield('page.content')
            </div>
        </section>
    </main>
@endsection