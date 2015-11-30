( function ( $ ) {
	QUnit.module( 'Thanks mobilediff' );

	QUnit.test( 'render button for logged in users', 1, function ( assert ) {
		var $container = $( '<div>' ),
			$user = $( '<div>' ).data( 'user-name', 'jon' )
				.data( 'revision-id', 1 )
				.data( 'user-gender', 'male' );

		mw.thanks._mobileDiffInit( $user, $container );
		assert.strictEqual( $container.find( 'button' ).length, 1, 'Thanks button was created.' );
	} );

}( jQuery ) );
