<template>
    <div class="movie-display overflow-hidden" v-touch:swipe="swipeHandler">

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
        <transition-group name="null" tag="div">
            <div v-for="(data, year) in shared.movies" :key="data.key" v-if="year == shared.currentYear">

                <div class='year-wrap width-100'>
                    <div class="year-wrap__content d-flex align-center justify-between">
                        <span class='year-wrap__title' v-text="year"></span>
                        <span v-if="shared.currentYear >= 2015" class="year-wrap__jump-link">
                            <a class="pointer d-flex" @click="scrollTo( 'streaming' )">streaming
                                <i class="ml-2 icon-right"></i>
                            </a>

                        </span>
                        <span v-if="admin" class='year-wrap__seen'><a href="\new">No. Watched <span class='year-wrap__seen-number' v-text="seenInYear( year )"></span></a></span>
                        <span v-if="!admin" class='year-wrap__seen'>No. Watched <span class='year-wrap__seen-number' v-text="seenInYear( year )"></span></span>
                    </div>
                </div>

                <section v-if="! data.movies" class="movie-list width-100 pos-relative">
                    <div v-for="n in 50" class='movie-wrap active'>
                        <div class="movie placeholder">
                            <div class='pad-buffer movie__title'>&nbsp;</div>
                        </div>
                    </div>
                </section>

                <section class="movie-list width-100 pos-relative" :class="shared.filter">
                    <div v-if="movie.type === 'theater'" v-for="(movie, index) in data.movies" class='movie-wrap' :class="{ active : movie.active }">
                        <div class="movie" :id="`movie-${movie.id}`" :data-rank="index + 1">
                            <div class='pad-buffer movie__title' v-html="movie.title" @click="toggleMovie( movie )"></div>
                            <a title='IMDB Link' :href="`https://google.com/search?q=imdb ${movie.year} ${movie.title}&btnI`" target='_blank' class='movie__link icon-link'></a>
                            <i v-if="admin && shared.editMode" @click="deleteMovie( movie )" class="icon-x movie-list__delete pointer"></i>
                        </div>
                    </div>
                </section>

                <div v-if="shared.currentYear >= 2015">
                    <div class='year-wrap width-100' v-if="shared.currentYear >= 2015">
                        <div id="streaming" class="year-wrap__content d-flex align-center">
                            STREAMING
                            <span class="year-wrap__jump-link">
                                <a class="pointer d-flex" @click="scrollTo( 'top' )">theatrical
                                    <i class="ml-2 icon-right"></i>
                                </a>

                            </span>
                        </div>
                    </div>

                    <section class="movie-list width-100 pos-relative" :class="shared.filter">
                        <div v-if="movie.type === 'streaming'" v-for="(movie, index) in data.movies" class='movie-wrap' :class="{ active : movie.active }">
                            <div class="movie" :id="`movie-${movie.id}`" :data-rank="index + 1">
                                <div class='pad-buffer movie__title' v-html="movie.title" @click="toggleMovie( movie )"></div>
                                <a title='IMDB Link' :href="`https://google.com/search?q=imdb ${movie.year} ${movie.title}&btnI`" target='_blank' class='movie__link icon-link'></a>
                                <i v-if="admin && shared.editMode" @click="deleteMovie( movie )" class="icon-x movie-list__delete pointer"></i>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </transition-group>
    </div>
</template>


<script>
    export default {
        name: 'movie-display',
        data() {
            return {
                shared : App.state,
                bufferYears : 2,
                swipeDirection : '',
                admin : false,
            };
        },
        created(){
            if( App.user === 1 ){
                this.admin = true;
            }
            this.shared.init( 'currentYear',  App.years.max );
            this.initMovieTree();
            this.setYear( this.getStartingYear() );
            App.event.on( 'setYear', this.setYear );
        },
        computed : {
        },
        methods : {

            getStartingYear(){
                let year = App.years.max;
                if( window.location.hash ){
                    let tempYear = parseInt( window.location.hash.replace( '#', '' ) );
                    if( this.shared.movies.hasOwnProperty( tempYear ) ){
                        year = tempYear;
                    }
                }

                return year;
            },

            deleteMovie( movie ){
                if( ! confirm( `Delete ${movie.title}?` ) ) return;
                App.event.event('working' );
                axios.delete( `/movie/${movie.id}` )
                    .then( response => {
                        console.log( response.data );
                        this.shared.movies[ this.shared.currentYear ].movies = this.shared.movies[ this.shared.currentYear  ].movies.filter( item => item.id != movie.id );
                    })
                    .catch( errors => console.log( errors ) )
                    .then( () => App.event.event('done' ) );
            },

            seenInYear( year ){
                if( !this.shared.movies || !this.shared.movies[ year ].movies ) return 0;
                let filtered = this.shared.movies[ year ].movies.filter( item => item.active );
                return filtered.length;
            },

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
                    years[i] = { movies : null, key : i };
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

                // add the year as a hash if its not the current year
                if( year != App.years.max ){
                    window.location.hash = '#' + year;
                }

                let oldYear = this.shared.currentYear;
                if( oldYear > year ){
                    this.swipeDirection = 'left';
                } else {
                    this.swipeDirection = 'right';
                }

                this.shared.currentYear = year;

                // Lets load the data for the given year, plus a number before and after equal to our buffer, if needed.
                let yearsToLoad = [];
                let startYear = year - this.bufferYears;
                let endYear = year + this.bufferYears;

                for( let i = startYear; i <= endYear; i++ ){
                    // loop through the current year, and the ones before and after and check if there
                    // is a null entry for it in the movies object. If so add it to the list of things to load
                    if( this.shared.movies.hasOwnProperty( i ) && this.shared.movies[i].movies === null ){
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
                            this.shared.movies[ year ].movies = response.data[ year ];
                        }
                        if( movie ){
                            Vue.nextTick( () => {
                                this.scrollTo( `movie-${movie.id}`);
                            });
                        } else {
                            this.scrollTo( 'top' );
                        }
                    })
                    .catch( errors => {
                        // log error
                        console.log( "Error status", errors.response.status );
                        console.log( "Error data", errors.response.data );
                        console.log( "Error headers", errors.response.headers );
                    })
                    .then( () => App.event.event('done' ) );
            }
        },
    }
</script>


<style>
</style>
