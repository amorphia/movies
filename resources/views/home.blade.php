@extends( 'layouts.layout' )

@push( 'header-scripts' )
    <script>
        App.years = {
            'max' : {{ $max }},
            'min' : {{ $min }}
        };

        App.user = {{ $user->id  }}

        App.seenTotal = {{ $seen_total }};
    </script>
@endpush

@section( 'page', 'home' )

@section( 'content' )
    <nav-menu></nav-menu>
    <movie-display></movie-display>
@endsection
