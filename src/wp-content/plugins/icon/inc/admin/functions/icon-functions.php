<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/*-------------------------------------------------------------------------------*/
/*   AJAX Get Feed List
/*-------------------------------------------------------------------------------*/
function icon_get_icons_ajax()
{

	// run a quick security check
    if ( ! check_ajax_referer( 'icon_get_icons', 'security' ) ) {
        return;
    }

    switch ( $_POST['library'] ) {

        case 'fontawesome':

            $icons = icon_fontawesome();

            break;

        case 'fontello':

            $icons = icon_fontello();

            break;

        case 'icomoon':

            $icons = icon_icomoon();

            break;

        case 'dashicons':

            $icons = icon_dashicons();

            break;

        case 'openiconic':

            $icons = icon_openiconic();

            break;

        case 'gmaterialicons':

            $icons = icon_materialicons();

            break;

        case 'justvector':

            $icons = icon_justvector();

            break;

        case 'paymentfont':

            $icons = icon_paymentfont();

            break;

        default:

            $icons = '';

    }

    echo json_encode( $icons ); //Send to Icons in Array

    die();

}

add_action( 'wp_ajax_icon_get_icons_ajax', 'icon_get_icons_ajax' );

/*-------------------------------------------------------------------------------*/
/*   Welcome screen to show HowTo
/*-------------------------------------------------------------------------------*/
function welcome_screen_do_activation_redirect()
{

	// Bail if no activation redirect
    if ( ! get_transient( '_welcome_screen_activation_redirect' ) ) {
        return;
    }

    // Delete the redirect transient
    delete_transient( '_welcome_screen_activation_redirect' );

	// Bail if activating from network, or bulk
    if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
        return;
    }

    // Redirect to bbPress about page
    wp_safe_redirect( add_query_arg( array( 'page' => 'icon-welcome' ), admin_url( 'admin.php' ) ) );

}

add_action( 'admin_menu', 'icon_menu_pages' );

function icon_menu_pages()
{

    add_menu_page( ICON_PLUGIN_NAME, ICON_PLUGIN_NAME, 'manage_options', 'icon-plugin', 'icon_dashboard_screen', plugins_url( 'assets/img/icon-picker.png', ( __DIR__ ) ), 21 );
    add_submenu_page( 'icon-plugin', 'How to use Icon Pro plugin?', 'Help', 'manage_options', 'icon-welcome', 'icon_welcome_screen_content' );

    if ( current_user_can( 'manage_options' ) ) {
        global $submenu;
        $submenu['icon-plugin'][0][0] = 'Dashboard';
    }

}

function icon_welcome_screen_content()
{
    ?>
  <div class="wrap">
  <style>
  .img-howto { width:550px;height:261px;border: 6px solid rgb(245, 245, 245);
-webkit-box-shadow: 0px 0px 7px 1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 7px 1px rgba(0,0,0,0.75);
box-shadow: 0px 0px 7px 1px rgba(0, 0, 0, 0.75); }
.dashhelp {margin-right:30px;}
.rtl .dashhelp {margin-right:0px;margin-left:30px;}
.icon-welcome-con {margin-bottom:30px;border-bottom: 1px solid #ccc; vertical-align:middle;}
.icon-welcome-h2 {color: rgb(81, 68, 68);font-size: 2.1em;line-height: 1.9em;}
.icon-welcome-h3 {color: rgb(81, 68, 68);font-size: 1.4em;line-height: 1.1em;}
.icon-welcome-h2 span {color: rgb(81, 68, 68);font-size: 2.1em;}
  </style>
    <div class="icon-welcome-con"><h2 class="icon-welcome-h2"><span class="dashicons dashicons-editor-help dashhelp"></span> How to use <i>Icon</i> plugin?</h2></div>
    <h3>How to Insert Icon?</h3>
    <p>
      <img class="img-howto" src="<?php echo ICON_URL.'/inc/admin/assets/img/how-to-insert.png'; ?>" />
    </p>
    <br />
    <h3>How to Edit Icon?</h3>
    <p>
      <img class="img-howto" src="<?php echo ICON_URL.'/inc/admin/assets/img/how-to-edit.png'; ?>" />
    </p>
  </div>
  <?php
}

function icon_dashboard_screen()
{

    add_thickbox();

    include ABSPATH.'wp-admin/includes/plugin-install.php';
    global $tabs, $tab, $paged, $type, $term;
    $tabs     = array();
    $tab      = 'search';
    $per_page = 30;
    $args     = array
        (
        'author'   => 'GhozyLab',
        'page'     => $paged,
        'per_page' => $per_page,
        'fields'   => array( 'last_updated' => true, 'downloaded' => true, 'icons' => true ),
        'locale'   => get_locale(),
    );
    $args = apply_filters( "install_plugins_table_api_args_$tab", $args );
    $api  = plugins_api( 'query_plugins', $args );
    $item = $api->plugins;

    $plugins_allowedtags = array(
        'a'    => array( 'href' => array(), 'title' => array(), 'target' => array() ),
        'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
        'code' => array(), 'pre'                                      => array(), 'em' => array(), 'strong' => array(),
        'div'  => array( 'class' => array() ), 'span'    => array( 'class' => array() ),
        'p'    => array(), 'ul'                                       => array(), 'ol' => array(), 'li'     => array(),
        'h1'   => array(), 'h2'                                       => array(), 'h3' => array(), 'h4'     => array(), 'h5' => array(), 'h6' => array(),
        'img'  => array( 'src' => array(), 'class' => array(), 'alt' => array() ),
    );

    ?>
	<form id="plugin-filter">

	<div class="wrap about-wrap icon-class">

			<h1><?php printf( esc_html__( 'Welcome to %s', 'icon' ), ICON_PLUGIN_NAME );?></h1>
			<div class="about-text"><?php printf( esc_html__( 'Thank you for installing this plugin. %s is ready to make your sites more stunning and more elegant!', 'icon' ), ICON_PLUGIN_NAME );?></div>
            <div id="generalpage" class="icon-badge">Version <?php echo ICON_VERSION; ?></div>
        <hr style="margin-bottom:20px;">

		<p id="todocs" style="border-bottom: 1px dotted #CCC; margin-top: 45px; padding-bottom: 5px;margin-bottom:30px;"><span class="dashicons dashicons-megaphone"></span>&nbsp;&nbsp;<?php _e( 'Several plugins below is free and you can install it and hopefully useful. Enjoy it!', 'icon' );?></p>
	<style>

        .toplevel_page_icon-plugin .about-wrap {
            max-width: 100% !important;
        }

	<?php
if ( is_rtl() ) {?>

	.icon-badge {
		padding-top: 150px;
		height: 128px;
		width: 128px;
		color: #989898;
		font-weight: normal;
		font-style:italic;
		font-size: 13px;
		text-align: center;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
		margin: 0 -5px;
		background: url('<?php echo plugins_url( 'assets/img/plugin-logo.png', ( __DIR__ ) ) ?>') no-repeat;
	}

	.about-wrap .icon-badge {
		position: absolute;
		top: 0;
		left: 0;
	}


	.icon-class .feature-section p {
		max-width: 100% !important;
	}

	.about-wrap .feature-section {
		margin-top: 50px;
	}

	.about-wrap .feature-section h4 {
		padding-bottom: 10px;
		display:inline-block;
		border-bottom: 1px dashed #CCC;
	}

	.about-wrap .feature-section .faq-question {
		border-bottom: 1px solid #CCC;
		font-weight:bold;
		color:#666;
	}

	.about-wrap .feature-section .faq-answer {
		font-style: normal;
		font-weight: normal;
		color:#666;
		font-size: 0.9em;
	}

	.icon-plugin-action-buttons {
		text-align:left;
		}
	.icon-plugin-action-buttons li {
		display: inline-block;
		margin-right: 1em;
		}

	.icon-button-con {
		padding-left: 15px;
		}

.icon-button-install {
background: none repeat scroll 100% 0% #2EA2CC !important;
border-color: #0074A2 !important;
box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.5) inset, 0px 1px 0px rgba(0, 0, 0, 0.15) !important;
color: #FFF !important;
}

.icon-button-install:focus {
    box-shadow: 0px 0px 0px 1px #5B9DD9, 0px 0px 2px 1px rgba(30, 140, 190, 0.8) !important;
}

.icon-button-install:hover {
    color: #FFF !important;
	background: none repeat scroll 100% 0% #5B9DD9 !important;
}

.icon-button-update {
background: none repeat scroll 100% 0% #E74F34 !important;
border-color: #C52F2F !important;
box-shadow: 0px 1px 0px rgba(255, 235, 235, 0.5) inset, 0px 1px 0px rgba(0, 0, 0, 0.15) !important;
color: #FFF !important;
}

.icon-button-update:focus {
    box-shadow: 0px 0px 0px 1px #DA3232, 0px 0px 2px 1px rgba(255, 140, 140, 0.8) !important;
}

.icon-button-update:hover {
    color: #FFF !important;
	background: none repeat scroll 100% 0% #DA3232 !important;
}

.drop-shadow {
    position:relative;
    background:#fff;
	margin-bottom:40px;
}

.drop-shadow:before,
.drop-shadow:after {
    content:"";
    position:absolute;
    z-index:-2;
}

/* Lifted corners */

.lifted {
    -moz-border-radius:4px;
         border-radius:4px;
}

.lifted:before,
.lifted:after {
    bottom:15px;
    right:10px;
    width:50%;
    height:20%;
    max-width:300px;
    -webkit-box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
       -moz-box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
            box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
    -webkit-transform:rotate(3deg);
       -moz-transform:rotate(3deg);
        -ms-transform:rotate(3deg);
         -o-transform:rotate(3deg);
            transform:rotate(3deg);
}

.lifted:after {
    left:10px;
    right:auto;
    -webkit-transform:rotate(-3deg);
       -moz-transform:rotate(-3deg);
        -ms-transform:rotate(-3deg);
         -o-transform:rotate(-3deg);
            transform:rotate(-3deg);
}

.icon-container-cnt .plugin-card {
    border: 1px solid #d7d7d7 !important;
}

.icon-container-cnt .plugin-card-bottom {
    border-top: 1px solid #c2c2c2 !important;
	background-color: #efefef !important;
}

.most_popular {top: -5px;position: absolute;width: 115px;height: 85px;background: transparent url("<?php echo plugins_url( 'assets/img/most_popular.png', ( __DIR__ ) ) ?>") no-repeat scroll right top;right: -5px;}

.icon-class .plugin-card-top h3 {
	margin-top: 0;
	}

.icon-class .plugin-card-bottom {
	font-size: 13px;
}

	.icon-free-plugin-icon {
		left: 0;
		right: auto;

	}

	<?php } else {?>

	.icon-badge {
		padding-top: 150px;
		height: 128px;
		width: 128px;
		color: #989898;
		font-weight: normal;
		font-style:italic;
		font-size: 13px;
		text-align: center;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
		margin: 0 -5px;
		background: url('<?php echo plugins_url( 'assets/img/plugin-logo.png', ( __DIR__ ) ) ?>') no-repeat;
	}

	.about-wrap .icon-badge {
		position: absolute;
		top: 0;
		right: 0;
	}


	.icon-class .feature-section p {
		max-width: 100% !important;
	}

	.about-wrap .feature-section {
		margin-top: 50px;
	}

	.about-wrap .feature-section h4 {
		padding-bottom: 10px;
		display:inline-block;
		border-bottom: 1px dashed #CCC;
	}

	.about-wrap .feature-section .faq-question {
		border-bottom: 1px solid #CCC;
		font-weight:bold;
		color:#666;
	}

	.about-wrap .feature-section .faq-answer {
		font-style: normal;
		font-weight: normal;
		color:#666;
		font-size: 0.9em;
	}

	.icon-plugin-action-buttons {
		text-align:right;
		}
	.icon-plugin-action-buttons li {
		display: inline-block;
		margin-left: 1em;
		}

	.icon-button-con {
		padding-right: 15px;
		}

.icon-button-install {
background: none repeat scroll 0% 0% #2EA2CC !important;
border-color: #0074A2 !important;
box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.5) inset, 0px 1px 0px rgba(0, 0, 0, 0.15) !important;
color: #FFF !important;
}

.icon-button-install:focus {
    box-shadow: 0px 0px 0px 1px #5B9DD9, 0px 0px 2px 1px rgba(30, 140, 190, 0.8) !important;
}

.icon-button-install:hover {
    color: #FFF !important;
	background: none repeat scroll 0% 0% #5B9DD9 !important;
}

.icon-button-update {
background: none repeat scroll 0% 0% #E74F34 !important;
border-color: #C52F2F !important;
box-shadow: 0px 1px 0px rgba(255, 235, 235, 0.5) inset, 0px 1px 0px rgba(0, 0, 0, 0.15) !important;
color: #FFF !important;
}

.icon-button-update:focus {
    box-shadow: 0px 0px 0px 1px #DA3232, 0px 0px 2px 1px rgba(255, 140, 140, 0.8) !important;
}

.icon-button-update:hover {
    color: #FFF !important;
	background: none repeat scroll 0% 0% #DA3232 !important;
}

.drop-shadow {
    position:relative;
    background:#fff;
	margin-bottom:40px;
}

.drop-shadow:before,
.drop-shadow:after {
    content:"";
    position:absolute;
    z-index:-2;
}

/* Lifted corners */

.lifted {
    -moz-border-radius:4px;
         border-radius:4px;
}

.lifted:before,
.lifted:after {
    bottom:15px;
    left:10px;
    width:50%;
    height:20%;
    max-width:300px;
    -webkit-box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
       -moz-box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
            box-shadow:0 15px 10px rgba(0, 0, 0, 0.7);
    -webkit-transform:rotate(-3deg);
       -moz-transform:rotate(-3deg);
        -ms-transform:rotate(-3deg);
         -o-transform:rotate(-3deg);
            transform:rotate(-3deg);
}

.lifted:after {
    right:10px;
    left:auto;
    -webkit-transform:rotate(3deg);
       -moz-transform:rotate(3deg);
        -ms-transform:rotate(3deg);
         -o-transform:rotate(3deg);
            transform:rotate(3deg);
}

.icon-container-cnt .plugin-card {
    border: 1px solid #d7d7d7 !important;
}

.icon-container-cnt .plugin-card-bottom {
    border-top: 1px solid #c2c2c2 !important;
	background-color: #efefef !important;
}

.most_popular {top: -5px;position: absolute;width: 115px;height: 85px;background: transparent url("<?php echo plugins_url( 'assets/img/most_popular.png', ( __DIR__ ) ) ?>") no-repeat scroll left top;right: -5px;}

.icon-class .plugin-card-top h3 {
	margin-top: 0;
	}

.icon-class .plugin-card-bottom {
	font-size: 13px;
}

<?php }
    ?>

        </style>

<div style="margin-top:30px;" class="wp-list-table widefat plugin-install">
	<div id="the-list">

		<?php

    foreach ( (array) $item as $plugin ) {

        if ( is_object( $plugin ) ) {
            $plugin = (array) $plugin;
        }

        $title = wp_kses( $plugin['name'], $plugins_allowedtags );
        // Remove any HTML from the description.
        $description = strip_tags( $plugin['short_description'] );
        $version     = wp_kses( $plugin['version'], $plugins_allowedtags );

        $name = strip_tags( $title.' '.$version );

        $author = wp_kses( $plugin['author'], $plugins_allowedtags );

        if ( ! empty( $author ) ) {
            $author = ' <cite>'.sprintf( __( 'By %s' ), $author ).'</cite>';
        }

        $action_links = array();

        if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
            $status = install_plugin_install_status( $plugin );

            switch ( $status['status'] ) {
                case 'install':

                    if ( $status['url'] ) {
                        /* translators: 1: Plugin name and version. */
                        $action_links[] = '<a class="install-now button-secondary icon-button-install" href="'.$status['url'].'" aria-label="'.esc_attr( sprintf( __( 'Install %s now' ), $name ) ).'">'.__( 'Install Now' ).'</a>';
                    }

                    break;
                case 'update_available':

                    if ( $status['url'] ) {
                        /* translators: 1: Plugin name and version */
                        $action_links[] = '<a class="button icon-button-update" href="'.$status['url'].'" aria-label="'.esc_attr( sprintf( __( 'Update %s now' ), $name ) ).'">'.__( 'Update Now' ).'</a>';
                    }

                    break;
                case 'latest_installed':
                case 'newer_installed':
                    $action_links[] = '<span class="button button-disabled" title="'.esc_attr__( 'This plugin is already installed and is up to date' ).' ">'._x( 'Installed', 'plugin' ).'</span>';
                    break;
            }

        }

        $details_link = self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='.$plugin['slug'].
            '&amp;TB_iframe=true&amp;width=750&amp;height=550' );

        /* translators: 1: Plugin name and version. */
        $action_links[] = '<a href="'.esc_url( $details_link ).'" class="thickbox" aria-label="'.esc_attr( sprintf( __( 'More information about %s' ), $name ) ).'" data-title="'.esc_attr( $name ).'">'.__( 'More Details' ).'</a>';

        if ( ! empty( $plugin['icons']['svg'] ) ) {
            $plugin_icon_url = $plugin['icons']['svg'];
        } elseif ( ! empty( $plugin['icons']['2x'] ) ) {
            $plugin_icon_url = $plugin['icons']['2x'];
        } elseif ( ! empty( $plugin['icons']['1x'] ) ) {
            $plugin_icon_url = $plugin['icons']['1x'];
        } else {
            $plugin_icon_url = $plugin['icons']['default'];
        }

        /**
         * Filter the install action links for a plugin.
         *
         * @since 2.7.0
         *
         * @param array $action_links An array of plugin action hyperlinks. Defaults are links to Details and Install Now.
         * @param array $plugin       The plugin currently being listed.
         */
        $action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
        ?>
		<div class="plugin-card drop-shadow lifted">
			<div class="plugin-card-top" style="min-height: 160px !important;">
            <?php
if ( isset( $plugin['slug'] ) && $plugin['slug'] == 'easy-media-gallery' ) {echo '<div class="most_popular"></div>';}
        ?>
				<a href="<?php echo esc_url( $details_link ); ?>" class="icon-free-plugin-icon thickbox plugin-icon"><img width="128" height="128" src="<?php echo esc_attr( $plugin_icon_url ) ?>" /></a>
				<div class="name column-name" style="margin-right: 20px !important;">
					<h3><a href="<?php echo esc_url( $details_link ); ?>" class="thickbox"><?php echo $title; ?></a></h3>
				</div>
				<div class="desc column-description" style="margin-right: 20px !important;">
					<p><?php echo $description; ?></p>
					<p class="authors"><?php echo $author; ?></p>
				</div>
			</div>
					<div class="icon-button-con">
					<?php

        if ( $action_links ) {
            echo '<ul class="icon-plugin-action-buttons">';
            echo '<li>'.$action_links[0].'</li>';

            switch ( $plugin['slug'] ) {
                case 'easy-media-gallery':
                    echo '<li><a class="button" aria-label="PRO VERSION DEMO" href="http://ghozylab.com/plugins/easy-media-gallery-pro/demo/" target="_blank">PRO VERSION DEMO</a></li>';
                    break;

                case 'image-slider-widget':
                    echo '<li><a class="button" aria-label="PRO VERSION DEMO" href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-at-the-bottom/" target="_blank">PRO VERSION DEMO</a></li>';
                    break;

                case 'easy-notify-lite':
                    echo '<li><a class="button" aria-label="PRO VERSION DEMO" href="http://ghozylab.com/plugins/easy-notify-pro/demo/" target="_blank">PRO VERSION DEMO</a></li>';
                    break;

                case 'contact-form-lite':
                    echo '<li><a class="button" aria-label="PRO VERSION DEMO" href="http://demo.ghozylab.com/plugins/easy-contact-form-plugin/contact-form-recaptcha/" target="_blank">PRO VERSION DEMO</a></li>';
                    break;

                case 'feed-instagram-lite':
                    echo '<li><a class="button" aria-label="PRO VERSION DEMO" href="http://demo.ghozylab.com/plugins/instagram-feed-plugin/" target="_blank">PRO VERSION DEMO</a></li>';
                    break;

                default:
                    break;
            }

            echo '</ul>';
        }

        ?>
				</div>
			<div class="plugin-card-bottom">
				<div class="column-updated">
					<strong><?php _e( 'Last Updated:' );?></strong> <span title="<?php echo esc_attr( $plugin['last_updated'] ); ?>">
						<?php printf( __( '%s ago' ), human_time_diff( strtotime( $plugin['last_updated'] ) ) );?>
					</span>
				</div>
<div class="column-downloaded">
					<?php echo sprintf( _n( '%s download', '%s downloads', $plugin['downloaded'] ), number_format_i18n( $plugin['downloaded'] ) ); ?>
				</div>
				<div class="column-compatibility">
					<?php

        if ( ! empty( $plugin['tested'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $plugin['tested'] ) ), $plugin['tested'], '>' ) ) {
            echo '<span class="compatibility-untested">'.__( 'Untested with your version of WordPress' ).'</span>';
        } elseif ( ! empty( $plugin['requires'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $plugin['requires'] ) ), $plugin['requires'], '<' ) ) {
            echo '<span class="compatibility-incompatible">'.__( '<strong>Incompatible</strong> with your version of WordPress' ).'</span>';
        } else {
            echo '<span class="compatibility-compatible">'.__( '<strong>Compatible</strong> with your version of WordPress' ).'</span>';
        }

        ?>
				</div>
			</div>
		</div>
		<?php
}

    ?>

     	</div>
	</div>
</div>
	</form>

<?php
}