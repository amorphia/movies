@extends( 'layout' )

@section( 'page', 'landing' )

@section( 'content' )



<main class='splash-logo mt-6 width-80 center-text pull-center d-flex flex-column'>

    <h1>MOVIE CHECKLIST</h1>

    <div class="p-5 width-30 pull-center">
        <a class="btn register-button mb-3 d-block" href="{{ route( 'login') }}">LOGIN</a>
        <a class="btn register-button d-block" href="{{ route( 'register') }}">REGISTER</a>
    </div>

</main>

<figure class="splash-logo-wrap pos-cover overflow-hidden" style="background-image: url( '/images/posters/{{ rand ( 1 , 6 )  }}.jpg')"></figure>



@endsection
