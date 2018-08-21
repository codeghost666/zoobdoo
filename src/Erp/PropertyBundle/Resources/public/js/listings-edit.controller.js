var ListingsEditController = function () {
    this.slider = $( '.carousel' );
    this.slidesNum = $( '.slides-num' );
    this.slidesNumCurrent = $( '.current-slide-num' );
    this.slidesItem = $( '.article-slide .item' );
    this.slideCurrent = $( '.carousel-indicators li.active') ;
    this.slidesNumber = this.slidesItem.length;
    this.currentSlide = parseInt( $( '.carousel-indicators li.active' ).attr( 'data-slide-to' ) ) + 1;
    this.itemsWrapper = $( '.carousel-indicators' );
    this.itemsWrapperWidth = ( this.slideCurrent.width() + parseInt( this.slideCurrent.css( 'margin-right' ) ) + parseInt( this.slideCurrent.css( 'margin-left' ) ) ) * this.slidesNumber;
};

ListingsEditController.prototype.newSlider = function() {
    this.slider.carousel({
        interval: false,
        cycle: true
    });

    this.itemsWrapper.width(this.itemsWrapperWidth);

    this.slider.swiperight(function() {  
        $( this ).carousel( 'prev' );  
    });  
    this.slider.swipeleft(function() {
        $( this ).carousel( 'next' );  
    });

    this.slider.on( 'slid.bs.carousel', function ( event ) {        
        var checkActiveInterval = setInterval(function() {
            if( $( '.carousel-indicators li.active' ).length) {
                var itemsWrapper = $( '.carousel-indicators' ),
                    currentCarousel = $( event.currentTarget ),
                    slideCurrent = $( '.carousel-indicators li.active' ),
                    slidesNumber = $( '.article-slide .item' ).length,
                    currentSlide = parseInt( slideCurrent.attr( 'data-slide-to' ) ) + 1,
                    itemsWrapperWidth = currentCarousel.find( ( slideCurrent ).width() + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ))) * currentCarousel.find( slidesNumber ) + 1;

                clearInterval(checkActiveInterval);
                $( '.current-slide-num' ).text( parseInt( slideCurrent.attr( 'data-slide-to' ) ) + 1 );
                if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' )) !== 0 ) {
                    if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) !== currentCarousel.find( slidesNumber ) - 1) {
                        currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) - 1) );
                    }              
                } else {
                    currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) ) );
                }
            }
        }, 100);        
    })

    if (this.slidesNumber !== 0) {
        this.slidesNum.text( this.slidesNumber );
        this.slidesNumCurrent.text( this.currentSlide );
    } else {
        this.slidesNum.text( '0' );
    }
};

ListingsEditController.prototype.run = function() {
    this.newSlider();
};

$(function () {
    var controller = new ListingsEditController();
    controller.run();
});
