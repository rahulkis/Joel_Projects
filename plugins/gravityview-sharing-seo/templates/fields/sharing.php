<?php
/**
 * Display the sharing field type.
 *
 * @package GravityView_Sharing
 */

	$sharing_class_name = $gravityview->field->sharing_service;
	$sharing = false;

	if( !empty( $sharing_class_name ) && class_exists( $sharing_class_name ) ) {
		$sharing = $sharing_class_name::getInstance()->output( $gravityview );
	} else {
		gravityview()->log->debug( 'Sharing class `{sharing_class_name}` not found', array( 'sharing_class_name' => $sharing_class_name ) );
		return;
    }

	// No result, or the shortcode didn't process, get outta here.
	if( empty( $sharing ) ) {
		gravityview()->log->debug( 'Sharing class `{sharing_class_name}` output was empty.', array( 'sharing_class_name' => $sharing_class_name ) );
		return;
	}

?>
<div class="gv-sharing-container">
	<?php echo $sharing; ?>
</div>
