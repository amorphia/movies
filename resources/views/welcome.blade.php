<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    </head>
    <body>
       <h1>Movie Tracker</h1>
       <form>
           <div class="form-group row">
               <div class="col-md-6 offset-md-4">
                   <a href="{{ url('/login/facebook') }}" class="btn btn-facebook"> Facebook</a>

               </div>
           </div>
       </form>
    </body>
</html>
