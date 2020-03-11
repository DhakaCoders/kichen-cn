jQuery(function($){
    $( '.login-limit-apply' ).apply_cancel()
    .on('apply.apply_cancel', function(e) {
        $( this ).apply_cancel( 'hide' );
        $( this ).parent().save_settings({
            data: { action : "admin_actions", WishListMemberAction : "save", },
            on_success: function( me, result ) {
                var i = $( me ).find( ':input[name=login_limit]' );
                i.data( 'initial', i.val() );
            },
            on_done: function( me, result ) {
                $( '.wlm-message-holder' ).show_message( {
                    message : result.msg,
                    type : result.msg_type
                } );
            }
        });
    })
    .on('cancel.apply_cancel', function(e) {
            $( this ).val( $( this ).data( 'initial' ) ).trigger( 'change' );
    });

    $( '.login-limit-error-apply' ).apply_cancel()
    .on('apply.apply_cancel', function(e) {
        $( this ).apply_cancel( 'hide' );
        $( this ).parent().save_settings({
            data: { action : "admin_actions", WishListMemberAction : "save", },
            on_success: function( me, result ) {
                var i = $( me ).find( ':input[name=login_limit_error]' );
                i.data( 'initial', i.val() );
            },
            on_done: function( me, result ) {
                $( '.wlm-message-holder' ).show_message( {
                    message : result.msg,
                    type : result.msg_type
                } );
            }
        });
    })
    .on('cancel.apply_cancel', function(e) {
            $( this ).val( $( this ).data( 'initial' ) ).trigger( 'change' );
    });
});