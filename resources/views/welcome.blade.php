@extends( 'layout' )

@section( 'page', 'landing' )

@section( 'content' )



<main class='splash mt-6 width-80 center-text pull-center d-flex flex-column pos-absolute-center'>

    <h1>MOVIE CHECKLIST</h1>

    <div class="splash__buttons p-5 width-30 pull-center">
        <a class="btn splash__button mb-3 d-block" href="{{ route( 'login') }}">LOGIN</a>
        <a class="btn splash__button d-block" href="{{ route( 'register') }}">REGISTER</a>
    </div>

</main>

<figure class="splash__background pos-cover overflow-hidden" style="background-image: url( '/images/posters/{{ rand ( 1 , 6 )  }}.jpg')"></figure>



@endsection
