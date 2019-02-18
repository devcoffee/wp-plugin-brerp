<?php ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
add_shortcode('user-logout', 'user_logout');
function user_logout() {
	session_destroy(); 
	header('Location: '.site_url().'/index.php/entrar');
}	
