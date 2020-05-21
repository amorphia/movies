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

</style>

