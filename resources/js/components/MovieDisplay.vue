<template>
<div class="movie-display" v-touch:swipe="swipeHandler">

    <nav class='hover-nav left-hover-nav' v-if="shared.movies.hasOwnProperty( shared.currentYear + 1 )">
        <div class='hover-arrow pos-absolute-center left-hover-arrow' @click="setYear( shared.currentYear + 1 )">
            <i class="icon-left pos-absolute-center"></i>
        </div>
    </nav>

    <nav class='hover-nav right-hover-nav' v-if="shared.movies.hasOwnProperty( shared.currentYear - 1 )">
        <div class='hover-arrow pos-absolute-center right-hover-arrow' @click="setYear( shared.currentYear - 1 )">
            <i class="icon-right pos-absolute-center"></i>
        </div>
    </nav>

    <div class='year-wrap width-100'>
        <div class="year-wrap__content d-flex align-center justify-between">
            <span class='year-wrap__title' v-text="shared.currentYear"></span>
            <span class='year-wrap__seen'>No. Watched <span class='year-wrap__seen-number' v-text="seenThisYear"></span></span>
        </div>
    </div>

    <section class="movie-list width-100 pos-relative" :class="shared.filter">
        <div v-for="(movie, index) in shared.movies[shared.currentYear]" class='movie-wrap' :class="{ active : movie.active }">
            <div class="movie" :id="`movie-${movie.id}`" :data-rank="index + 1">
                <div class='pad-buffer movie__title' v-html="movie.title" @click="toggleMovie( movie )"></div>
                <a title='IMDB Link' :href="`https://duckduckgo.com/?q=\\imdb ${movie.year} ${movie.title}`" target='_blank' class='movie__link icon-link'></a>
            </div>
        </div>
    </section>
</div>
</template>


<script>
    export default {

        name: 'movie-display',

        data() {
            return {
                shared : App.state,
                bufferYears : 1
            };
        },

        created(){
            this.shared.init( 'currentYear',  App.years.max );
            this.initMovieTree();
            this.setYear( App.years.max );
            App.event.on( 'setYear', this.setYear );
        },

        computed : {
            seenThisYear(){
                if( !this.shared.movies || !this.shared.movies[ this.shared.currentYear ] ) return 0;

                let filtered = this.shared.movies[ this.shared.currentYear ].filter( item => item.active );
                return filtered.length;
            },

        },

        methods : {

            swipeHandler( direction ){
                let newYear;

                if( direction === 'left' ){
                    newYear = this.shared.currentYear - 1;
                }

                if( direction === 'right'){
                    newYear = this.shared.currentYear + 1;
                }

                if( newYear && this.shared.movies.hasOwnProperty( newYear ) ){
                    this.setYear( newYear );
                }

            },

            setMovieSeen( movie ){
                this.shared.seenTotal++;
                this.shared.recentMovies.unshift( movie );
                if( this.shared.recentMovies.length > 50 ){
                    this.shared.recentMovies.pop();
                }
            },

            setMovieUnseen( movie ){
                this.shared.seenTotal--;
                this.shared.recentMovies = this.shared.recentMovies.filter( item => item.id !== movie.id );
            },

            toggleMovie( movie ){

                movie.active = !movie.active;

                if( movie.active ){
                    this.setMovieSeen( movie );
                } else {
                    this.setMovieUnseen( movie );
                }

                App.event.event('working' );

                axios.post( `/movie/${movie.id}/toggle`, { active : movie.active } )
                    .then( response => {
                        console.log( response.data );
                    })
                    .catch( errors => console.log( errors ) )
                    .then( () => App.event.event('done' ) );

            },

            initMovieTree(){

                // build years object
                let years = {};
                for( let i = App.years.max; i >= App.years.min; i-- ){
                    years[i] = null;
                }

                // init shared movie tree
                App.state.init( 'movies', years );
            },

            setYear( data ){
                let year;
                let movie;

                if( typeof data === 'object' && data !== null){
                    movie = data;
                    year = parseInt( movie.year );
                } else {
                    year = parseInt( data );
                }

                // if that year doesn't exist, abort
                if( !this.shared.movies.hasOwnProperty( year ) ) return;

                this.shared.currentYear = year;

                // Lets load the data for the given year, plus a number before and after equal to our buffer, if needed.
                let yearsToLoad = [];
                let startYear = year - this.bufferYears;
                let endYear = year + this.bufferYears;
                console.log( `Start: ${startYear} End: ${endYear}`);
                for( let i = startYear; i <= endYear; i++ ){
                    // loop through the current year, and the ones before and after and check if there
                    // is a null entry for it in the movies object. If so add it to the list of things to load
                    if( this.shared.movies.hasOwnProperty( i ) && this.shared.movies[i] === null ){
                        yearsToLoad.push( i );
                    }
                }

                // if we have any years to load, then lets load them
                if( yearsToLoad.length > 0 ){
                    this.loadYears( yearsToLoad, movie );
                } else if( movie ){
                    this.scrollTo( `movie-${movie.id}`);
                } else {
                    this.scrollTo( 'top' );
                }
            },

            scrollTo( id ){
                let element = document.getElementById( id );
                this.$scrollTo( element );
                element.classList.add( "flash" );
            },

            loadYears( years, movie ){

                App.event.event('working' );

                axios.post( '/movies/fetch', { years : years })
                    .then( response => {

                        for( let year in response.data ){
                            this.shared.movies[ year ] = response.data[ year ];
                        }

                        if( movie ){
                            Vue.nextTick( () => {
                                this.scrollTo( `movie-${movie.id}`);
                            });
                        } else {
                            this.scrollTo( 'top' );
                        }
                    })
                    .catch( errors => console.log( errors ) )
                    .then( () => App.event.event('done' ) );

            }

        },

    }
</script>


<style>

</style>

