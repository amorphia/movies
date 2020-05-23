@extends( 'layouts.layout' )

@section( 'page', 'landing' )

@section( 'content' )

<main class='splash mt-6 width-80 pull-center d-flex flex-column pos-absolute-center'>

    @yield( 'main' )

</main>

<figure class="splash__background pos-cover overflow-hidden" style="background-image: url( '/images/posters/{{ rand ( 1 , 6 )  }}.jpg')"></figure>



@endsection
