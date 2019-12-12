/*
 * Custom JS
 */

$(document).ready(function () {
    var emails = [];
    $.ajax({
        url: '/users/all',
        success: function (data) {
            $.each(data, function (index, value) {
                emails.push(value.email);
            });
        }
    });

    $("#fieldUserId").autocomplete({
        source: emails,
        minLength: 3,
    })
});

function split( val ) {
    return val.split( /,\s*/ );
}
function extractLast( term ) {
    return split( term ).pop();
}

$(document).ready(function () {
    var products = [];
    $.ajax({
        url: '/products/all',
        success: function (data) {
            $.each(data, function (index, value) {
                products.push(value.product_name);
            });
        }
    });

    $("#fieldProducts").on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
        }
    }).autocomplete({
        source: function( request, response ) {
            // delegate back to autocomplete, but extract the last term
            response( $.ui.autocomplete.filter(
                products, extractLast( request.term ) ) );
        },
        minLength: 2,
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function( event, ui ) {
            var terms = split( this.value );
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push( "" );
            this.value = terms.join( "," );
            return false;
        }
        // multiselect: true,
    })
});

