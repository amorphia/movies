window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let getToken = function(){

    let token = document.querySelector('meta[name="csrf-token"]');

    if ( token && token.content && token.content !== 'loading...' ) {
        console.log('csrf loaded: ' + token.content );
        App.csrf = token.content;
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
        setTimeout( getToken, 100 );
    }
}

getToken();


/**
 * Import Vue
 */
window.Vue = require('vue');
window.Vue.config.devtools = true;

/**
 * Set core app object
 */
window.App = window.App || {};

/**
 *  Moment
 */
window.Moment = require('moment');

/**
 *  Swipe Events
 */

window.Vue2TouchEvents = require('vue2-touch-events');

window.isTouchDevice = function isTouchDevice() {
    return 'ontouchstart' in window        // works on most browsers
        || navigator.maxTouchPoints;       // works on IE10/11 and Surface
};

if( isTouchDevice() ){
    Vue.use( Vue2TouchEvents, {
        swipeTolerance: 100,
    })
} else {
    Vue.directive('touch', {});
}


/**
 * Scroll To
 */

window.VueScrollTo = require('vue-scrollto');


// You can also pass in the default options
Vue.use( VueScrollTo, {
    container: "body",
    duration: 500,
    easing: "ease",
    offset: -300,
    force: true,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});

