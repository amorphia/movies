<template>
<div>
    <transition name="slide">
        <div v-if="recentOpen" class="recent-movies pos-fixed width-100">
            <i class="recent-movies__close icon-x" @click="recentOpen = false"></i>
            <div class="recent-movies__content d-flex flex-wrap width-100">
                <div v-for="(movie, index) in shared.recentMovies" class="recent-movies__item">
                    <div class='margin'><span>{{ index + 1 }}</span>{{ movie.title }}</div>
                </div>
            </div>
        </div>
    </transition>

    <nav class="nav d-flex pos-fixed width-100 align-stretch justify-center">

        <!-------- MENU --------->
        <div class="view-menu nav__item mobile-only">
            <i class="nav__icon icon-menu"></i>
        </div>

        <!-------- RECENTS --------->
        <div class="view-recents nav__item pointer" @click="recentOpen = true">
            <i class="nav__icon icon-recent"></i>
        </div>

        <!-- year select -->
        <div class="view-year nav__item grow-2" title="Switch years">
            <i class="nav__icon icon-year"></i>
            <select v-if="shared.movies" class="nav__input year-select" :value="shared.currentYear" @change="setYear">
                <option v-for="year in movieYears" :value='year' v-text="year"></option>
            </select>
        </div>


        <!-- Search -->
        <movie-search></movie-search>


        <!-- filter select -->
        <div class="view-filter nav__item grow-1" title="Filter movies">
            <i class="nav__icon icon-show"></i>
            <select class="nav__input nav-select" v-model="shared.filter">
                <option value="all">all</option>
                <option value="unseen">unseen</option>
                <option value="seen">seen</option>
            </select>
        </div>

        <div class="total-movies nav__item" v-text="shared.seenTotal"></div>
    </nav>
</div>
</template>


<script>
    import MovieSearch from "./MovieSearch";
    export default {

        name: 'nav-menu',
        components: {MovieSearch},
        data() {
            return {
                shared : App.state,
                recentOpen : false,
                searchInput : '',
            };
        },

        created(){
            this.shared.init( 'filter', 'all' );
            this.shared.init( 'seenTotal', App.seenTotal );
            this.loadRecentMovies();
        },

        methods : {

            loadRecentMovies(){
                axios.get( `/movies/recent` )
                    .then( response => {
                        console.log( response.data );
                        this.shared.init( 'recentMovies', response.data );
                    })
                    .catch( errors => console.log( errors ) );
            },

            setYear( e ){
                App.event.event( 'setYear', e.target.value );
            }
        },

        computed: {

            searchResults(){

            },

            movieYears(){
                let years = [];
                for( let year in this.shared.movies ){
                    years.unshift( year );
                }
                return years;
            }
        }
    }
</script>


<style>

</style>

