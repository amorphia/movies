export default class Product {

    constructor( item ) {
        this.quantity = item.quantity;
        this.shop = item.element.shop.id;
        this.link = this.search( item.links, 'type', 'continueShopping', 'href' );
        this.product = this.search( item.element.properties, 'key', 'product' );
        this.design = this.getDesignFromUrl( this.link );
    }

    // given a url, attempt to parse the design ID from it
    getDesignFromUrl( url ){
        let design;
        let expression = /\/([A-Za-z0-9-]*)-a-/;
        let designArray = expression.exec( url );

        if( designArray ){
            design = designArray[1];
        }

        return design;
    }

    search( array, key, keyValue, valueKey = 'value' ){
        let element = array.find( element => element.hasOwnProperty( key ) && element[key] === keyValue );
        return element[valueKey];
    }

}




