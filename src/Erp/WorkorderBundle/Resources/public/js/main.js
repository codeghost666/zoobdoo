

    $("#jsGrid").jsGrid({
        height: "100%",
        width: "100%",
        filtering: false,
        inserting: true,
        editing: true,
        sorting: true,
        paging: true,
        autoload: true,
        pageSize: 15,
        pageButtonCount: 5,
        onRefreshed: function(args) {
            var items = args.grid.option("data");

            var subTotal = 0;
            var tax = 0;

            items.forEach(function(item){
                subTotal += item.hours * item.rate;
                tax += item.tax_code / 10;
            });

            var total = subTotal + tax;

            $("#sub_total").text('Sub Total   :   ' + subTotal + ' USD');
            $("#tax_total").text('Tax(10%)   :   ' + tax + ' USD');
            $("#total").text('Total   :   ' + total + ' USD');

            $("#serviceData").val(JSON.stringify(items));
        },
        //controller: db,
        fields: [
            { name: "task_name", title: "Service", type: "text", validate: "required", width: 150 },
            {
                name: "hours",
                title: "Hours",
                type: "number",
                validate: "required",
                width: 50,
                insertTemplate: function() {
                    var input = this.__proto__.insertTemplate.call(this);
                    input.val('0')
                    return input;
                }
            },
            {
                name: "rate",
                title: "Rate",
                type: "number",
                validate: "required",
                width: 50,
                insertTemplate: function() {
                    var input = this.__proto__.insertTemplate.call(this);
                    input.val('0')
                    return input;
                }

            },
            { name: "tax_code", title: "Tax", type: "text", width:100},
            {
                name: "amount", title:"Amount", type: "label", editing:false,
                itemTemplate: function(value, item) {
                    return item.hours*item.rate;
                }
            },
            { type: "control", modeSwitchButton: false, editButton: false }
        ]
    });

    /*$.widget( "custom.combobox", {
        _create: function() {
            this.wrapper = $( "<span>" )
                .addClass( "custom-combobox" )
                .insertAfter( this.element );

            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },

        _createAutocomplete: function() {
            var selected = this.element.children( ":selected" ),
                value = selected.val() ? selected.text() : "";

            this.input = $( "<input>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: $.proxy( this, "_source" )
                })
                .tooltip({
                    classes: {
                        "ui-tooltip": "ui-state-highlight"
                    }
                });

            this._on( this.input, {
                autocompleteselect: function( event, ui ) {
                    ui.item.option.selected = true;
                    this._trigger( "select", event, {
                        item: ui.item.option
                    });
                },

                autocompletechange: "_removeIfInvalid"
            });
        },

        _createShowAllButton: function() {
            var input = this.input,
                wasOpen = false;

            $( "<a>" )
                .attr( "tabIndex", -1 )
                .attr( "title", "Show All Items" )
                .tooltip()
                .appendTo( this.wrapper )
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "custom-combobox-toggle ui-corner-right" )
                .on( "mousedown", function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
                .on( "click", function() {
                    input.trigger( "focus" );

                    // Close if already visible
                    if ( wasOpen ) {
                        return;
                    }

                    // Pass empty string as value to search for, displaying all results
                    input.autocomplete( "search", "" );
                });
        },

        _source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            response( this.element.children( "option" ).map(function() {
                var text = $( this ).text();
                if ( this.value && ( !request.term || matcher.test(text) ) )
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }) );
        },

        _removeIfInvalid: function( event, ui ) {

            // Selected an item, nothing to do
            if ( ui.item ) {
                return;
            }

            // Search for a match (case-insensitive)
            var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
            this.element.children( "option" ).each(function() {
                if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                    this.selected = valid = true;
                    return false;
                }
            });

            // Found a match, nothing to do
            if ( valid ) {
                return;
            }

            // Remove invalid value
            this.input
                .val( "" )
                .attr( "title", value + " didn't match any item" )
                .tooltip( "open" );
            this.element.val( "" );
            this._delay(function() {
                this.input.tooltip( "close" ).attr( "title", "" );
            }, 2500 );
            this.input.autocomplete( "instance" ).term = "";
        },

        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });

    $( "#erp_workorderbundle_edit_contractor" ).combobox(); */