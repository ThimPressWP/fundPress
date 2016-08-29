;
( function ( $ ) {

    // global
    window.TP_Donate_Global = {
        hooks: {action: {}, filter: {}},
        addAction: function ( action, callback, priority, context ) {
            this.addHook( 'action', action, callback, priority, context );
        },
        addFilter: function ( action, callback, priority, context ) {
            this.addHook( 'filter', action, callback, priority, context );
        },
        doAction: function ( action ) {
            this.doHook( 'action', action, arguments );
        },
        applyFilters: function ( action ) {
            return this.doHook( 'filter', action, arguments );
        },
        removeAction: function ( action, callback, priority, context ) {
            this.removeHook( 'action', action, callback, priority, context );
        },
        removeFilter: function ( action, callback, context ) {
            this.removeHook( 'filter', action, callback, context );
        },
        addHook: function ( hookType, action, callback, priority, context ) {
            priority = parseInt( ( priority || 10 ), 10 );
            if ( undefined == this.hooks[hookType][action] ) {
                this.hooks[hookType][action] = [ ];
            }
            var hooks = this.hooks[hookType][action];
            if ( undefined == context ) {
                context = action + '_' + hooks.length;
            }
            this.hooks[hookType][action].push( {callback: callback, priority: priority, context: context} );
        },
        doHook: function ( hookType, action, args ) {
            args = Array.prototype.slice.call( args, 1 );
            var value = args[0];
            if ( undefined != this.hooks[hookType][action] ) {
                var hooks = this.hooks[hookType][action];
                hooks.sort( function ( a, b ) {
                    return a['priority'] - b['priority'];
                } );
                for ( var i = 0; i < hooks.length; i++ ) {
                    var hook = hooks[i];
                    if ( typeof hook.callback == 'string' ) {
                        hook.callback = window[hook.callback];
                    }
                    if ( 'action' == hookType ) {
                        hook.callback.apply( hook.context, args );
                    } else {
                        args.unshift( value );
                        value = hook.callback.apply( hook.context, args );
                    }
                }
            }
            if ( 'filter' == hookType ) {
                return value;
            }
        },
        removeHook: function ( hookType, action, callback, priority, context ) {
            if ( undefined != this.hooks[hookType][action] ) {
                var hooks = this.hooks[hookType][action];
                for ( var i = hooks.length - 1; i >= 0; i-- ) {
                    var hook = hooks[i];
                    if ( hook.priority == priority && context == hook.context ) {
                        hooks.splice( i, 1 );
                    }
                }
                this.hooks[hookType][action] = hooks;
            }
        },
        beforeAjax: function () {
            $( '.donate_ajax_overflow' ).addClass( 'active' );
        },
        afterAjax: function () {
            $( '.donate_ajax_overflow' ).removeClass( 'active' );
        }
    };

} )( jQuery );