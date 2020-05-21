window.App.event = new class {

    constructor() {
        this._vue = new Vue();
    }

    event( event, data = null ){
        this._vue.$emit( event, data );
    }

    on( event, callback ){
        this._vue.$on( event, callback );
    }
};
