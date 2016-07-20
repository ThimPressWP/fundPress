( function ( $ ) {

    /**
     * DONATE_Site object
     * @type Object
     */
    DONATE_Site = {
        init: function () {
            /**
             * load form action
             */
            this.load_donate_form();

            /* validate checkout form */
            this.validate_checkout_form();

            /**
             * submit on lightbox
             */
            this.donate_submit();

            // load percent
            this.generate_percent();
        },
        /**
         * load donate form
         * @return null
         */
        load_donate_form: function () {
            /*
             * load form on click
             */
            $( document ).on( 'click', '.donate_load_form, .donate_button_title', function ( event ) {
                event.preventDefault();

                var _self = $( this ),
                        _campaign_id = _self.attr( 'data-campaign-id' ),
                        _data = {
                            action: 'donate_load_form',
                            nonce: thimpress_donate.nonce
                        };

                if ( typeof _campaign_id !== 'undefined' ) {
                    _data.campaign_id = _campaign_id;
                }

                $.ajax( {
                    url: thimpress_donate.ajaxurl,
                    type: 'POST',
                    data: _data,
                    beforeSend: function () {
                        TP_Donate_Global.beforeAjax();
                    }
                } ).done( function ( res ) {
                    TP_Donate_Global.afterAjax();

                    if ( typeof res.status !== 'undefined' && res.status === 'success' ) {
                        var _tmpl = wp.template( 'donate-form-template' );

                        $( '#donate_hidden' ).addClass( 'active' ).html( _tmpl( res ) );

                        $.magnificPopup.open( {
                            type: 'inline',
                            items: {
                                src: '#donate_hidden'
                            },
                            callbacks: {
                                open: function () {
                                    var timeout = setTimeout( function () {
                                        $( '#donate_hidden input[name="donate_input_amount"]:first' ).focus();
                                        $( '#donate_hidden input[name="payment_method"]:first' ).attr( 'checked', true );
                                        clearTimeout( timeout );
                                    }, 100 );
                                }
                            }
                        } );
                    }

                } );

            } );

        },
        /* validate checkout fields */
        validate_checkout_form: function () {
            var form = $( '.donate_form' ),
                    fields = form.find( 'input, textarea' );

            for ( var i = 0; i < fields.length; i++ ) {
                var field = $( fields[i] );
                field.blur( function () {
                    var input = $( this );
                    if ( input.hasClass( 'required' ) ) {
                        if ( input.val() === '' || ( input.hasClass( 'email' ) && new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test( input.val() ) === false ) ) {
                            input.removeClass( 'validated' ).addClass( 'donate_input_invalid' );
                        } else {
                            input.removeClass( 'donate_input_invalid' ).addClass( 'validated' );
                        }
                    }
                } );
            }
        },
        donate_submit: function () {
            $( document ).on( 'submit', '.donate_form', function ( e ) {
                e.preventDefault();

                var _form = $( this ),
                        _layout = _form.find( '.donate_form_layout' ),
                        _message = _form.find( '.donation-messages' );

                // remove old message error
                _form.find( '.donate_form_error_messages' ).remove();
                // invalid fields
                if ( _form.find( 'input[name="payment_method"]:checked' ).val() === 'stripe' ) {
                    Donate_Stripe_Payment.load_form( _form );
                } else {
                    // process ajax
                    var _data = _form.serializeArray( _form );

                    $.ajax( {
                        url: thimpress_donate.ajaxurl,
                        type: 'POST',
                        data: _data,
                        beforeSend: function () {
                            TP_Donate_Global.beforeAjax();
                        }
                    } ).done( function ( res ) {
                        TP_Donate_Global.afterAjax();

                        res = TP_Donate_Global.applyFilters( 'donate_submit_submit_form_completed', res );
                        if ( typeof res.status === 'undefined' ) {
                            return;
                        }

                        if ( typeof res.form !== 'undefined' && typeof res.args !== 'undefined' && res.form === true ) {
                            // process with authorize.net SIM payment
                            var args = res.args;
                            if ( Object.keys( args ).length !== 0 ) {
                                var html = [ ];
                                html.push( '<form id="donate_form_instead" action="' + res.url + '" method="POST">' )
                                $.each( args, function ( name, value ) {
                                    html.push( '<input type="hidden" name="' + name + '" value="' + value + '" />' );
                                } );
                                html.push( '<button type="submit" class="donate-redirecting">' + res.submit_text + '</button>' );
                                html.push( '</form>' );
                                _form.replaceWith( html.join( '' ) );
                                $( '#donate_form_instead' ).submit();
                            }
                        } else if ( res.status === 'success' && typeof res.url !== 'undefined' ) {
                            window.location.href = res.url;
                        } else if ( res.status === 'failed' && typeof res.message !== 'undefined' ) {
                            DONATE_Site.generate_messages( res.message );
                            $( 'body, html' ).animate( {
                                scrollTop: $( '.donation-messages' ).offset().top
                            } );
                        }
                    } );
                }

                return false;
            } );
        },
        donate_on_lightbox: function () {
            if ( typeof thimpress_donate.settings !== 'undefined' &&
                    typeof thimpress_donate.settings.checkout !== 'undefined' &&
                    typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
                    thimpress_donate.settings.checkout.lightbox_checkout === 'yes' ) {
                return true;
            }

            return false;
        },
        generate_messages: function ( messages ) {
            var form = $( '.donate_form_layout' );
            if ( form.find( '.donation-messages' ).length === 1 ) {
                $( '.donation-messages' ).replaceWith( messages );
            } else {
                form.prepend( messages );
            }
        },
        beforeAjax: function ( _form ) {
            if ( typeof _form === 'undefined' )
                return;

            _form.find( '.donate_button' ).addClass( 'donate_button_processing' );
        },
        afterAjax: function ( _form ) {
            if ( typeof _form === 'undefined' )
                return;

            _form.find( '.donate_button' ).removeClass( 'donate_button_processing' );
        },
        generate_percent: function () {
            var percents = $( '.donate_counter_percent' );
            for ( var i = 0; i < percents.length; i++ ) {
                var percent = $( percents[i] ),
                        percent_width = percent.attr( 'data-percent' ),
                        counter = percent.parent( '.donate_counter:first' ),
                        counter_width = counter.outerWidth(),
                        tootip = percent.find( '.donate_percent_tooltip' ),
                        tootip_width = tootip.outerWidth();

                percent.css( {
                    width: percent_width + '%'
                } );

                if ( tootip_width / 2 >= percent.outerWidth() ) {
                    tootip.css( {
                        left: 0
                    } );
                } else if ( ( tootip_width / 2 + percent.outerWidth() ) <= counter_width ) {
                    tootip.css( {
                        left: percent.outerWidth() - tootip_width / 2
                    } );
                } else if ( ( tootip_width / 2 + percent.outerWidth() ) > counter_width ) {
                    tootip.css( {
                        left: ( counter_width - tootip_width )
                    } );
                }
            }
        }
    };

    $( document ).ready( function () {
        DONATE_Site.init();
    } );

    $( window ).resize( function () {
        DONATE_Site.generate_percent();
    } );

} )( jQuery );
