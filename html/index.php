<?php

require_once 'classes/Membership.php';
$membership = New Membership();

$membership->confirm_Member();

	
	require ( 'layout/header.php' );

	require ( 'layout/menu.php' );

echo '<div id="content">';
		$pageAffiche = 'page_accueil';
		if ( isset( $_GET['page'] ) && $_GET['page'] != '' && file_exists( 'layout/' . $_GET['page'] . '.php' ) ){
			$pageAffiche = $_GET['page'];
		}
		require ( 'layout/' . $pageAffiche . '.php');
	echo '</div>';


	require ( 'layout/footer.php' );

?>