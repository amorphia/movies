
addtoOnLoad( () => {

    let gclid = getParameter( 'gclid' );

    // If we've already set the first touch, but we have a new gclid lets update it
    if( App.cookie( 'firsttouch' ) && gclid ){
        axios.get( `/admin/visitor/gclid/${gclid}`);
        return;
    }

    // If we've already set our first touch, abort
    if( App.cookie( 'firsttouch' ) ){
        return;
    }

    App.ifCsrf( function () {
        axios.post( '/admin/visitor/first-touch', {
            referrer : App.referrer,
            landing : App.landing,
            gclid : gclid
        });
    })

});


// returns a get parameter value from the current url
function getParameter( parameter ){
    let match = RegExp('[?&]' + parameter + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent( match[1].replace(/\+/g, ' ') );
}

