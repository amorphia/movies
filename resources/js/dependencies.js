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

/**
 * Set core app object
 */
window.App = window.App || {};

