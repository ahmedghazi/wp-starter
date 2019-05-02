<?php
/**
 * The header template
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package themeHandle
 */
?>

<!DOCTYPE html>
 
<!--[if lt IE 9]>
<html id="ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/assets/images/icons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

    <!-- stylesheets are enqueued via functions.php -->

    <!-- all other scripts are enqueued via functions.php -->
    <!--[if lt IE 9]>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/vendor/html5shiv.js" type="text/javascript"></script>
    <![endif]-->

    <?php // Lets other plugins and files tie into our theme's <head>:
    wp_head(); ?>
    <script>
        var ajaxUrl = "<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php";
        var baseDir = "<?php bloginfo('url'); ?>";
        //var templateDir = "<?php echo get_template_directory_uri(); ?>";
    </script>
</head>
 
<body <?php body_class(); ?>>
	<div id="page">
        <div>
            <input type="hidden" name="bc" value="<?php echo implode(" ", get_body_class()); ?>">
        </div>
		<header id="site-header" role="banner" class="">   
            <div class="row">
                <div class="col-md-4">
                    <nav class="access" role="navigation">
                        <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
                    </nav><!-- #access -->  
                </div>    
                <div class="col-md-4 text-center">
                    <a href="<?php echo esc_url( home_url() ); ?>" class="logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" onerror="this.onerror=null; this.src='<?php echo get_template_directory_uri(); ?>/assets/images/logo.png'" alt="<?php bloginfo('name'); ?>">
                    </a>
                </div>    
                <div class="col-md-4 text-right">col right</div>    
            </div>
            <div class="burger-wrap">
                <div class="burger xs-only">
                    <i></i>
                </div>
            </div>
		</header><!-- #branding -->


		<main id="main">