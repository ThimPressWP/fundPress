<?php
/**
 * messages template
 */
if( ! defined( 'ABSPATH' ) ) exit();
?>

<?php foreach( $messages as $status => $message ) : ?>
	<?php donate_get_template( 'notices/' . $status . '.php', array( 'messages' => $message ) ); ?>
<?php endforeach; ?>
