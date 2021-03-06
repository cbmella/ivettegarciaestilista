<?php
/**
 * Iniciando funciones del tema
 *
 * @package		RisingPhoenex
 * @author		Agencia Digital Reactor <contacto@reactor.cl>
 * @version		1.0.0
 */

/**
 * Remove Query Strings From Static Resources
 */
function mb_remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
/**
 * Registrando JS para front de la página - Footer
 */
function ss_scripts() {
    if ( ! is_admin() ) {
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-migrate');
    }
    //$main_js = filemtime(get_stylesheet_directory() . '/js/app.min.js');
    //wp_register_script('main-script', get_template_directory_uri() . '/js/app.min.js', array(), '1.'.date ("Ymd", $main_js).'.'.date ("His", $main_js), true);
    //wp_enqueue_script('main-script');
}


/**
 * Excerpt más elegante
 */
function new_excerpt_more($excerpt) {
	return str_replace('[...]', ' ', $excerpt);
}
/**
 * Quitando Width y Height de todas las imagenes - Sin esto el responsive no sirve
 */
function remove_width_attribute( $html ) {
	 $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
	 return $html;
}


/**
 * Agregar soporte para subir un svg
 */
function custom_upload_mimes($existing_mimes = array()) {
		$existing_mimes['svg'] = 'image/svg+xml';
		$existing_mimes['svgz'] = 'image/svg+xml';
		return $existing_mimes;
}

add_filter('upload_mimes', 'custom_upload_mimes');

/**
 * ACF: Options page
 */
/*
if (function_exists('acf_add_options_page')) {
                acf_add_options_page(array(
				'page_title'    => 'Footer',
				'menu_title'    => 'Footer',
				'menu_slug'     => 'opciones-footer-web',
				'icon_url'      => 'dashicons-hammer',
				'capability'    => 'edit_posts',
				'redirect'      => false
		));
                
                acf_add_options_page(array(
				'page_title'    => 'Header',
				'menu_title'    => 'Header',
				'menu_slug'     => 'opciones-header-web',
				'icon_url'      => 'dashicons-hammer',
				'capability'    => 'edit_posts',
				'redirect'      => false
		));
                
}
*/
/*
 * Ocultar barra de abministración
 *  */
add_filter('show_admin_bar', '__return_false');
/**
 * Limpiando nombre de archivo antes de la subida
 * @param  string $filename pasamos el nombre del archivo
 * @return string           nos retorna el nombre del archivo sin caracteres especiales
 */
function sanitize_filename_on_upload($filename) {
	$exp = explode('.',$filename);
	$ext = end($exp);
	// Replace all weird characters
	$sanitized = preg_replace('/[^a-zA-Z0-9-_.]/','', substr($filename, 0, -(strlen($ext)+1)));
	// Replace dots inside filename
	$sanitized = str_replace('.','-', $sanitized);
	return strtolower($sanitized.'.'.$ext);
}

add_filter('sanitize_file_name', 'sanitize_filename_on_upload', 10);
function css_acf() {
    echo '<style>.acf-field .acf-label {margin: 0 0 25px;}</style>';
}
/**
 * Colocando Créditos en footer de Wordpress
 */
function modify_footer_admin () {
	echo 'Creado por <a href="http://europapress.cl/">Europa Press</a>. Potenciado por <a href="http://www.wordpress.org">WordPress</a>';
}
/**
 * Colocando logo de Saul en pantalla de login
 */
function login_styles() {
	echo '<style type="text/css">body.login #login h1 a { background: url('. get_bloginfo('template_directory') .'/img/wdt_logo.png) no-repeat center top; height:146px; width:326px; margin-top: -50px;}</style>';
}

/**
 * Asignando dirección y Título al link del login
 */
function ss_url_login(){
	return 'http://europapress.cl/'; // The root of your site or any relative link
}
function ss_url_title(){
	return 'Europa Press'; // The title of your link
}

function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}

/*
 * Agregar permisos a los usuarios Editor para administrar usuarios.
 *  */

function add_cap_editor() {
    $perfil = get_role('editor');
    $perfil->remove_cap('edit_users');
    $perfil->remove_cap('delete_users');
    $perfil->remove_cap('create_users');
    $perfil->remove_cap('list_users');
    $perfil->remove_cap('remove_users');
    $perfil->remove_cap('add_users');
    $perfil->remove_cap('promote_users');
    $perfil->remove_cap('add_users');
    $perfil->remove_cap('manage_network_plugins');  
    // Editar opciones del tema
    $perfil->remove_cap('edit_theme_options');
}
add_action('admin_init', 'add_cap_editor');

add_action('admin_init', 'user_profile_fields_disable');
function user_profile_fields_disable() {
    global $pagenow;
    if ($pagenow ==='users.php' || $pagenow === 'user-new.php') {
        add_action( 'admin_footer', 'user_list_fields_disable_js' );
        return;
    }
    if ($pagenow!=='profile.php' && $pagenow!=='user-edit.php') {
        return;
    }
    if (current_user_can('administrator')) {
        return;
    }
    add_action( 'admin_footer', 'user_profile_fields_disable_js' );
}


/**
 * Disables selected fields in WP Admin user profile (profile.php, user-edit.php)
 */
function user_profile_fields_disable_js() {
?>
    <script>
        jQuery(document).ready( function($) {
            var fields_to_disable = ['role'];
            for(i=0; i<fields_to_disable.length; i++) {
                if ( $('#'+ fields_to_disable[i]).length ) {
                    $('#'+ fields_to_disable[i]).attr("disabled", "disabled");
                }
            }
        });
    </script>
<?php
}

/**
 * Remove option administrator in WP Admin user profile (users.php, user-new.php)
 */
function user_list_fields_disable_js() {
?>
    <script>
        jQuery(document).ready( function($) {
            var fields_to_disable = ['new_role', 'role'];
            for(i=0; i<fields_to_disable.length; i++) {
                if ( $('#'+ fields_to_disable[i]).length ) {
                    $('#'+ fields_to_disable[i]).find("option[value='administrator']").remove();
                }
            }
        });
    </script>
<?php
}

/**
 * Funciones para poder indexar en el buscador los custom fields
 * https://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
 */
function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
function cf_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
function cf_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' atrás' : 'justo ahora';
}

/* add_action( 'admin_menu', 'apk_eliminar_admin_menu_links' ); */

function apk_eliminar_admin_menu_links() {

   /*  $user = wp_get_current_user() */; //Obtenemos los datos del usuario actual

    /* if ( ! $user->has_cap( 'manage_options' ) ) { */ // Si es que el usuario no tiene rol de administrador
/*         remove_menu_page('edit.php'); 
        remove_menu_page('edit-comments.php'); 
        remove_menu_page('tools.php'); */
   /*  } */
}
