window.App.state = {

    debug: false,
    csrf: null,
    cart: [],

    errorReport( error ) {
        console.log( error.message );
        console.log( error.data );
    },

    init( property, value ) {

        if ( this.debug ){
            console.log( `set ${property} to:` );
            console.log( value );
        }

        let propertyArr = property.split('.');
        property = propertyArr.pop();
        let object = this;

        propertyArr.forEach( prop => {

            try {
                // if our current object variable isn't actually an object itself run away
                if( ! _.isObject( object ) ){
                    throw { message : `Invalid object`, data: object };
                }

                // if our current object variable does in fact have the appropriate property,
                // set our object variable to that property, otherwise throw an error
                if( prop in object ){
                    object = object[ prop ];
                } else {
                    throw { message : `Invalid Property: ${ prop } missing from:`, data: object };
                }

            } catch ( error ) {
                this.errorReport( error );
            }
        });

        try {
            // check to make sure we haven't already initialized this object and
            // if we haven't the set it up and make sure its reactive using Vue.set
            if( property in object ) {
                throw { message : `Cannot Initiate existing property: ${ property } on:`, data: object };
            } else {
                Vue.set( object, property, value );
            }
        } catch ( error ) {
            this.errorReport( error );
        }
    },


}



