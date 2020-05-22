<template>
    <div class="loader-bar pos-fixed width-100" v-if="requests > 0">
        <div class="loader-bar__streak"></div>
    </div>
</template>


<script>
    export default {

        name: 'working-slider',

        data() {
            return {
                shared : App.state,
                requests : 0
            };
        },

        created() {
            App.event.on('working', data => {
                this.requests++;
            } );

            App.event.on('done', () => {
                this.requests--;
                console.log( 'request done' );
            } );
        }
    }
</script>


<style>

.loader-bar {
    position: fixed !important;
    z-index: 100000;
}

.loader-bar {
    height: 5px;
    width: 100%;
    position: relative;
}

.loader-bar__streak {
    position: relative;
    height: 100%;
    animation-name: loader-animation;
    animation-duration: 3s;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}

@keyframes loader-animation {
    0% {
        width: 0%;
        background-color: #ff00fa;
    }
    25% {
        width: 100%;
        left: 0%
    }
    50% {
        left: 100%;
        width: 0;
        background-color: gold;
    }
    75% {
        left: 0%;
        width: 100%
    }
    100% {
        left: 0%;
        width: 0%;
        background-color: #ff00fa;
    }
}
</style>

