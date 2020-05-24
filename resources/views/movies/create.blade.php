@extends( 'layouts.layout' )

@section( 'page', 'landing' )

@section( 'content' )

    <main class='splash mt-6 width-80 pull-center d-flex flex-column'>

        <h1 class="center-text"><a href="/home">HOME</a></h1>
        <div class="splash__buttons p-5 width-40 pull-center">
            <create-movie action="/movie"></create-movie>
        </div>

    </main>

    <figure class="splash__background pos-cover overflow-hidden" style="background-image: url( '/images/posters/{{ rand ( 1 , 6 )  }}.jpg')"></figure>



@endsection

