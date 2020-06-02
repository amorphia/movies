window.App.ajax = new class {

    file( url, originalData, file, message ){
        // set multipart form headers
        let headers = { headers: { 'Content-Type': 'multipart/form-data' } };

        // build form data manually
        let data = new FormData();

        for ( let property in originalData ) {
            data.set( property, originalData[property] );
        }

        // add file to formdata
        data.append( "image", file );

        // post request
        return this.axios( 'post', url, data, message, headers )
    }

    post( url, data, message ){
        return this.axios( 'post', url, data, message );
    }

    get( url, message ){
        return this.axios( 'get', url, {}, message );
    }

    patch( url, data, message ){
        data = data || {};
        data._method = 'patch';
        return this.axios( 'post', url, data, message );
    }

    delete( url, data, message ){
        data = data || {};
        data._method = 'delete';
        return this.axios( 'post', url, data, message );
    }

    axios( type, url, data, message, headers ){

        // start working slider
        App.event.event('working');

        // return an axios call wrapped in a promise
        return new Promise(function( resolve, reject ) {
            axios[type]( url, data )
                .then( response => {
                    // notify success
                    let successMessage = message ? message : 'Success';
                    App.event.event( 'notify', { message : successMessage });

                    // resolve
                    resolve( response );
                } )
                .catch( error => {
                    // log error
                    console.log( "Error status", error.response.status );
                    console.log( "Error data", error.response.data );
                    console.log( "Error headers", error.response.headers );
                    
                    // notify error
                    App.event.event( 'notify', { message : 'Failed', error : true });

                    // reject
                    reject( error );
                } )
                .then( () => App.event.event('done') ); // end working slider

        });

    }

};

