var AvailablePropertiesController = function() {
    this.selectControl = $( '.select-control' );
    this.selectArrow = $( '.select2-selection__arrow' );
    this.filterForm = $( 'form#property-search-form' );
};

AvailablePropertiesController.prototype.initSelect = function() {
    var self = this;

    self.selectControl.select2();
    self.selectArrow.hide();
    $( window ).resize(function() {
        self.selectControl.select2();
        self.selectArrow.hide();
    }.bind( this ) );
};

AvailablePropertiesController.prototype.run = function() {
    this.initSelect();
};

$( function () {
    var controller = new AvailablePropertiesController();
    controller.run();
});
