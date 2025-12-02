<?php
/**
 * Twenty Seventeen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

/**
 * Twenty Seventeen only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function twentyseventeen_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentyseventeen
	 * If you're building a theme based on Twenty Seventeen, use a find and replace
	 * to change 'twentyseventeen' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentyseventeen' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'twentyseventeen-featured-image', 2000, 1200, true );

	add_image_size( 'twentyseventeen-thumbnail-avatar', 100, 100, true );

	add_image_size( 'avatar', 512, 512, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'top'    => __( 'Top Menu', 'twentyseventeen' ),
		'social' => __( 'Social Links Menu', 'twentyseventeen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
 	 */
	add_editor_style( array( 'assets/css/editor-style.css', twentyseventeen_fonts_url() ) );

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(
		'widgets' => array(
			// Place three core-defined widgets in the sidebar area.
			'sidebar-1' => array(
				'text_business_info',
				'search',
				'text_about',
			),

			// Add the core-defined business info widget to the footer 1 area.
			'sidebar-2' => array(
				'text_business_info',
			),

			// Put two core-defined widgets in the footer 2 area.
			'sidebar-3' => array(
				'text_about',
				'search',
			),
		),

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts' => array(
			'home',
			'about' => array(
				'thumbnail' => '{{image-sandwich}}',
			),
			'contact' => array(
				'thumbnail' => '{{image-espresso}}',
			),
			'blog' => array(
				'thumbnail' => '{{image-coffee}}',
			),
			'homepage-section' => array(
				'thumbnail' => '{{image-espresso}}',
			),
		),

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => array(
			'image-espresso' => array(
				'post_title' => _x( 'Espresso', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/espresso.jpg', // URL relative to the template directory.
			),
			'image-sandwich' => array(
				'post_title' => _x( 'Sandwich', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/sandwich.jpg',
			),
			'image-coffee' => array(
				'post_title' => _x( 'Coffee', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/coffee.jpg',
			),
		),

		// Default to a static front page and assign the front and posts pages.
		'options' => array(
			'show_on_front' => 'page',
			'page_on_front' => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set the front page section theme mods to the IDs of the core-registered pages.
		'theme_mods' => array(
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "top" location.
			'top' => array(
				'name' => __( 'Top Menu', 'twentyseventeen' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "social" location.
			'social' => array(
				'name' => __( 'Social Links Menu', 'twentyseventeen' ),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters Twenty Seventeen array of starter content.
	 *
	 * @since Twenty Seventeen 1.1
	 *
	 * @param array $starter_content Array of starter content.
	 */
	$starter_content = apply_filters( 'twentyseventeen_starter_content', $starter_content );

	add_theme_support( 'starter-content', $starter_content );
}
add_action( 'after_setup_theme', 'twentyseventeen_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function twentyseventeen_content_width() {

	$content_width = $GLOBALS['content_width'];

	// Get layout.
	$page_layout = get_theme_mod( 'page_layout' );

	// Check if layout is one column.
	if ( 'one-column' === $page_layout ) {
		if ( twentyseventeen_is_frontpage() ) {
			$content_width = 644;
		} elseif ( is_page() ) {
			$content_width = 740;
		}
	}

	// Check if is single post and there is no sidebar.
	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) {
		$content_width = 740;
	}

	/**
	 * Filter Twenty Seventeen content width of the theme.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param $content_width integer
	 */
	$GLOBALS['content_width'] = apply_filters( 'twentyseventeen_content_width', $content_width );
}
add_action( 'template_redirect', 'twentyseventeen_content_width', 0 );

/**
 * Register custom fonts.
 */
function twentyseventeen_fonts_url() {
	$fonts_url = '';

	/**
	 * Translators: If there are characters in your language that are not
	 * supported by Libre Franklin, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$libre_franklin = _x( 'on', 'Libre Franklin font: on or off', 'twentyseventeen' );

	if ( 'off' !== $libre_franklin ) {
		$font_families = array();

		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function twentyseventeen_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'twentyseventeen-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'twentyseventeen_resource_hints', 10, 2 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentyseventeen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'twentyseventeen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'twentyseventeen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'twentyseventeen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentyseventeen_widgets_init' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function twentyseventeen_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
									esc_url( get_permalink( get_the_ID() ) ),
									/* translators: %s: Name of current post */
									sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ), get_the_title( get_the_ID() ) )
								 );
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'twentyseventeen_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function twentyseventeen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentyseventeen_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function twentyseventeen_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'twentyseventeen_pingback_header' );

/**
 * Display custom color CSS.
 */
function twentyseventeen_colors_css_wrap() {
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) {
		return;
	}

	require_once( get_parent_theme_file_path( '/inc/color-patterns.php' ) );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );
?>
<style type="text/css" id="custom-theme-colors" <?php if ( is_customize_preview() ) { echo 'data-hue="' . $hue . '"'; } ?>>
	<?php echo twentyseventeen_custom_colors_css(); ?>
</style>
<?php }
add_action( 'wp_head', 'twentyseventeen_colors_css_wrap' );

/**
 * Enqueue scripts and styles.
 */
function twentyseventeen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentyseventeen-fonts', twentyseventeen_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'twentyseventeen-style', get_stylesheet_uri() );

	// Load the dark colorscheme.
	if ( 'dark' === get_theme_mod( 'colorscheme', 'light' ) || is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-colors-dark', get_theme_file_uri( '/assets/css/colors-dark.css' ), array( 'twentyseventeen-style' ), '1.0' );
	}

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'twentyseventeen-style' ), '1.0' );
		wp_style_add_data( 'twentyseventeen-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentyseventeen-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'twentyseventeen-style' ), '1.0' );
	wp_style_add_data( 'twentyseventeen-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentyseventeen-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '1.0', true );

	$twentyseventeen_l10n = array(
		'quote'          => twentyseventeen_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) ) {
		wp_enqueue_script( 'twentyseventeen-navigation', get_theme_file_uri( '/assets/js/navigation.js' ), array( 'jquery' ), '1.0', true );
		$twentyseventeen_l10n['expand']         = __( 'Expand child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['collapse']       = __( 'Collapse child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['icon']           = twentyseventeen_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) );
	}

	wp_enqueue_script( 'twentyseventeen-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );

	wp_enqueue_script( 'jquery-scrollto', get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ), array( 'jquery' ), '2.1.2', true );

	wp_localize_script( 'twentyseventeen-skip-link-focus-fix', 'twentyseventeenScreenReaderText', $twentyseventeen_l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/* Begin Custom Enqueues */

	/* Theme Stylesheet */
	wp_enqueue_style( 'scss-styles', get_template_directory_uri() . '/scss/style.scss', array(), date('Ymdhis') );

	/* jQuery Cycle2 */    
	wp_enqueue_script( 'jquery-cycle', get_template_directory_uri() . '/assets/js/jquery.cycle2.min.js', array( 'jquery' ), '20240105', true );

	/* jQuery Cycle2 Carousel */
	wp_enqueue_script ( 'cycle-carousel', get_template_directory_uri() . '/assets/js/jquery.cycle2.carousel.min.js', array( 'jquery' ), '20240105', true );
	
	/* jQuery Fancybox */
	wp_enqueue_script ( 'fancybox', get_template_directory_uri() . '/assets/js/fancybox/dist/jquery.fancybox.min.js', array( 'jquery' ), '20240105', true );
	wp_enqueue_style( 'fancybox-styles', get_template_directory_uri() . '/assets/js/fancybox/dist/jquery.fancybox.min.css', array(), '20240105' );

	/* jQuery ImagesLoaded */
	wp_enqueue_script ( 'images-loaded', get_template_directory_uri() . '/assets/js/imagesloaded.pkgd.min.js', array( 'jquery' ), '20240105', true );

	if(is_page('118')) {
		/* PHP Calendar Script */
		wp_enqueue_script ( 'php-calendar', get_template_directory_uri() . '/assets/advanced-event-calendar-system-php/advanced-nodb/Calendar.js', array( 'jquery' ), '20240105', true );

		/* PHP Calendar Sytles */
		wp_enqueue_style( 'php-calendar-styles', get_template_directory_uri() . '/assets/advanced-event-calendar-system-php/advanced-nodb/calendar.css', array(), '20240105' );
			/* PHP Calendar Sytles */
		wp_enqueue_style( 'php-calendar-base-styles', get_template_directory_uri() . '/assets/advanced-event-calendar-system-php/advanced-nodb/style.css', array(), '20240105' ); 
	}

	/* jQuery Stellar Parallax */
	wp_enqueue_script ( 'stellar-parallax', get_template_directory_uri() . '/assets/js/jquery.stellar.min.js', array( 'jquery' ), '20240105', true );

	/* jQuery Masonry */
	wp_enqueue_script ( 'masonry', get_template_directory_uri() . '/assets/js/masonry.pkgd.min.js', array( 'jquery' ), '20240105', true );

	/* Fonts on Fonts on Fonts */
	//	 wp_enqueue_style ( 'google-fonts', '//fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@100;300;400;500;700;800&family=Mynerve&family=Ultra&display=swap', array(), '20161027' );

	wp_enqueue_style ( 'font-awesome', get_template_directory_uri() . '/assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css', array(''), '20240105' );    

	wp_enqueue_style ( 'ion-icons', get_template_directory_uri() . '/assets/fonts/ionicons-2.0.1/css/ionicons.min.css' );  

	wp_enqueue_script( 'site-functions', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), date('Ymdhis'), true );

	wp_enqueue_script('jquery-tabs', '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery', 'jquery-ui-core') );

	wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css', array('jquery', 'jquery-ui-core') );
}
add_action( 'wp_enqueue_scripts', 'twentyseventeen_scripts' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentyseventeen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 740 <= $width ) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) {
		if ( ! ( is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) {
			$sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentyseventeen_content_image_sizes_attr', 10, 2 );

/**
 * Filter the `sizes` value in the header image markup.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function twentyseventeen_header_image_tag( $html, $header, $attr ) {
	if ( isset( $attr['sizes'] ) ) {
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'twentyseventeen_header_image_tag', 10, 3 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array $attr       Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size       Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function twentyseventeen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( is_archive() || is_search() || is_home() ) {
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	} else {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentyseventeen_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function twentyseventeen_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'twentyseventeen_front_page_template' );

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * Customizer additions.
 */
require get_parent_theme_file_path( '/inc/customizer.php' );

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path( '/inc/icon-functions.php' );


/**
 * Begin Custom Functions
 */

/* Add ACF Options Page */
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();	
}


add_filter( 'gform_pre_render_1', 'populate_checkbox' );
add_filter( 'gform_pre_validation_1', 'populate_checkbox' );
add_filter( 'gform_pre_submission_filter_1', 'populate_checkbox' );
add_filter( 'gform_admin_pre_render_1', 'populate_checkbox' );
add_filter( 'gform_pre_render_7', 'populate_checkbox' );
add_filter( 'gform_pre_validation_7', 'populate_checkbox' );
add_filter( 'gform_pre_submission_filter_7', 'populate_checkbox' );
add_filter( 'gform_admin_pre_render_7', 'populate_checkbox' );
function populate_checkbox( $form ) {

	foreach( $form['fields'] as &$field )  {
		$field_id = 11;
		if ( $field->id != $field_id ) {
			continue;
		}

		$pets = array();
		$current_user_id = get_current_user_id();
		$args = array(
			'post_type' => 'dog',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'owner',
					'value'   => $current_user_id,
				),
			),
		);
		$user_dogs = new WP_Query($args);
		if($user_dogs->have_posts()) {
			while($user_dogs->have_posts()) {
				$user_dogs->the_post();
				$pets[] = get_the_title();
			}
		}
		wp_reset_postdata();

		$input_id = 1;
		foreach( $pets as $pet ) {

			//skipping index that are multiples of 10 (multiples of 10 create problems as the input IDs)
			if ( $input_id % 10 == 0 ) {
				$input_id++;
			}

			$choices[] = array( 'text' => $pet, 'value' => $pet );
			$inputs[] = array( 'label' => $pet, 'id' => "{$field_id}.{$input_id}" );

			$input_id++;
		}

		$field->choices = $choices;
		$field->inputs = $inputs;

	}

	return $form;
}


/* Dynamically Populate the Contact Form for logged in users */

add_filter( 'gform_field_value_user_phone', 'get_user_phone' );
function get_user_phone( $value ) {
    $user = get_current_user_id();
		$user_phone = get_field('phone', 'user_' . $user);
		return $user_phone;
}

add_filter( 'gform_field_value_user_dogs', 'get_user_dogs' );
function get_user_dogs( $value ) {
    $user = get_current_user_id();
		$pets = array();
		$user = get_current_user_id();
		if(have_rows('dogs', 'user_' . $user)) {
			while(have_rows('dogs', 'user_' . $user)) {
				the_row();
				$pets[] = get_sub_field('name');
			}
		}
		return implode(', ', $pets);
}



/* Phone number formatter */
function format_phone_number($phone) {
	$numbers = explode("\n", $phone);
	foreach($numbers as $number)
	{
		print preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '$1-$2-$3', $number). "\n";
	}
}

/* fetch age from dob */
function getAge($dob)
{
	$today = strtotime('today UTC-4');
	$age = abs($today - strtotime($dob));
	$years = floor($age / (365*60*60*24));
	$months = floor(($age - $years * 365*60*60*24) / (30*60*60*24));
	return $years .' years, ' . $months . ' months';
}

/* Add Dogs column to Users backend post list */
function modify_user_table( $column ) {
	$column['dogs'] = 'Dogs';
	return $column;
}
add_filter( 'manage_users_columns', 'modify_user_table' );

function modify_user_table_row( $val, $column_name, $user_id ) {
	$dogs_array = array();
	if(have_rows('dogs', 'user_' . $user_id)) {
		while(have_rows('dogs', 'user_' . $user_id)) {
			the_row();
			$dogs_array[] = get_sub_field('name');
		}
	}
	$dogs = implode(', ', $dogs_array);
	if ( 'dogs' == $column_name ) {
		return $dogs;
	}
	return $val;
}
add_filter( 'manage_users_custom_column', 'modify_user_table_row', 10, 3 );

/* Add Booking Post on Form Submit */
add_action( 'gform_advancedpostcreation_post_after_creation_7', 'fetch_dog_ids', 10, 4 );
function fetch_dog_ids($post_id, $feed, $entry, $form) {
	$current_user_id = get_current_user_id();
	$dogs = array();
	if(!empty(rgar( $entry, '11.1'))) {
		$dogs[] = rgar( $entry, '11.1' );
	}
	if(!empty(rgar( $entry, '11.2' ))) { 
		$dogs[] = rgar( $entry, '11.2' );
	}
	if(!empty(rgar( $entry, '11.3' ))) { 
		$dogs[] = rgar( $entry, '11.3' );
	}
	if(!empty(rgar( $entry, '11.4' ))) { 
		$dogs[] = rgar( $entry, '11.4' );
	}
	if(!empty(rgar( $entry, '11.5' ))) { 
		$dogs[] = rgar( $entry, '11.5' );
	}
	$booking_post_title = rgar( $entry, '3') . ' - ' . rgar( $entry, '4') . ': ' . implode(', ', $dogs) . ' for ' . rgar( $entry, '18' );
	
	$post_update = array(
    'ID'         => $post_id,
    'post_title' => $booking_post_title,
		'post_status' => 'draft',
  );

  wp_update_post( $post_update );
	
	$dogs_involved_ids = array();
	foreach($dogs as $dog_name) {
		$dog_id = new WP_Query(
			array(
				'post_type' => 'dog',
				'posts_per_page' => -1,
				'title' => $dog_name,
				'meta_query'     => array(
					array(
						'key'     => 'owner',
						'value'   => $current_user_id,
					),
				),
			),
		);
		if($dog_id->have_posts()) {
			while($dog_id->have_posts()) {
				$dog_id->the_post();
				$dogs_involved_ids[] = get_the_ID();
			}
		}
		wp_reset_postdata();
	}
	update_field('field_741b4a5e5016c', $dogs_involved_ids, $post_id);
}

///* Add Booking to User Profile Booking Field */
//add_action( 'gform_after_submission_1', 'add_booking_to_user', 10, 2 );
//function add_booking_to_user($entry, $form) {
//	$user = get_current_user_id();
//	$field_key = 'field_641b49af50168';
//	$dogs = array();
//	if(!empty(rgar( $entry, '11.1'))) {
//		$dogs[] = rgar( $entry, '11.1' );
//	}
//	if(!empty(rgar( $entry, '11.2' ))) { 
//		$dogs[] = rgar( $entry, '11.2' );
//	}
//	if(!empty(rgar( $entry, '11.3' ))) { 
//		$dogs[] = rgar( $entry, '11.3' );
//	}
//	if(!empty(rgar( $entry, '11.4' ))) { 
//		$dogs[] = rgar( $entry, '11.4' );
//	}
//	if(!empty(rgar( $entry, '11.5' ))) { 
//		$dogs[] = rgar( $entry, '11.5' );
//	}
//	$value = array(
//		array(
//			'field_641b49ea50169' => rgar( $entry, '3' ),
//			'field_641b4a155016a'   => rgar( $entry, '4' ),
//			'field_643f5693b6ecc' => rgar( $entry, '5' ),
//			'field_643f56a2b6ecd'   => rgar( $entry, '6' ),
//			'field_641b4a235016b' => rgar( $entry, '18' ),	
//			'field_641b4a5e5016c' => implode(', ', $dogs)
//		)
//	);
//	$row = array(
//		'field_641b49ea50169' => rgar( $entry, '3' ),
//		'field_641b4a155016a'   => rgar( $entry, '4' ),
//		'field_643f5693b6ecc' => rgar( $entry, '5' ),
//		'field_643f56a2b6ecd'   => rgar( $entry, '6' ),
//		'field_641b4a235016b' => rgar( $entry, '18' ),	
//		'field_641b4a5e5016c' => implode(', ', $dogs)
//	);
//	if(have_rows($field_key, 'user_' . $user)) {
//		add_row($field_key, $row, 'user_' . $user);
//	} else {
//		update_field( $field_key, $value, 'user_' . $user );
//	}
//}

/* Blackout dates */
function get_blackout_dates() {
	$blackout_dates = array();
	while(have_rows('blackout_dates', 'option')) {
		the_row();
		$blackout_dates[] = get_sub_field('blackout_date');
	}
	
	$dogs_booked = array();
	$args = array(
		'post_type' => 'booking',
		'posts_per_page' => -1,
	);
	$bookings = new WP_Query($args);
	while($bookings->have_posts()) {
		$bookings->the_post();
		$end_date = '';
		$start_date = DateTime::createFromFormat('Y-m-d', get_field('booking_start_date'));
		$one_day = new DateInterval('P1D');
		if(!empty(get_field('booking_end_date'))) {
			$end_date = get_field('booking_end_date');
		} else {
			$end_date = $start_date->add($one_day);
		}
		$period = new DatePeriod(
			new DateTime(get_field('booking_start_date')),
			new DateInterval('P1D'),
			new DateTime(),
			DatePeriod::INCLUDE_END_DATE
		);
		foreach ($period as $key => $value) {
			$booking_date = $value->format('Y-m-d');
			//$dogs = explode( ',', get_field('dogs_involved') );
			$dogs = get_field('dogs_involved');
			$dogs_booked[$booking_date] = empty( $dogs_booked[$booking_date] ) ? $dogs : array_merge( $dogs_booked[$booking_date], $dogs );
		}
	}
	wp_reset_postdata();
	$booked_up = array_keys( array_filter( $dogs_booked, function( $dogs ) {
		return count( $dogs ) >= 3;
	}));
			
	$blackout_dates = array_unique( array_merge( $blackout_dates, $booked_up ) );
	return $blackout_dates;
}


/* Build a PHP Calendar */
function create_calendar() {
	require_once( __DIR__ . '/assets/advanced-event-calendar-system-php/advanced-nodb/Calendar.class.php' );
	$booking_dates = array();
	$args = array(
		'post_type' => 'booking',
		'posts_per_page' => -1,
	);
	$bookings = new WP_Query($args);
	if($bookings->have_posts()) {
		while($bookings->have_posts()) {
			$bookings->the_post();
			$service = get_field('service');
			$color = '';
			if($service == 'Dog Boarding') {
				$color = '#DE9611';
			} else {
				$color = '#524131';
			}
			$dogs = get_field('dogs_involved');
			$start_date = get_field('booking_start_date');
			$end_date = get_field('booking_end_date');
			if(!$end_date) {
				$end_date = $start_date;
			}
			$dropoff_time = date('H:i:s', strtotime(get_field('drop-off_time')));
			$pickup_time = date('H:i:s', strtotime(get_field('pick-up_time')));
			if(!$dropoff_time || $dropoff_time == '00:00:00') {
				$dropoff_time = '08:00:00';
			}
			if(!$pickup_time || $pickup_time == '00:00:00') {
				$pickup_time = '08:00:00';
			}
			$user_bookings = array();
			$user_bookings['service'] = $service;
			$dog_names = array();
			$dog_links = array();
			foreach($dogs as $dog_id) {
				$dog_names[] = get_the_title($dog_id);
				$dog_links[] = get_the_permalink($dog_id);
			}
			$user_bookings['dogs'] = implode(', ', $dog_names);
			$user_bookings['dog_links'] = ($dog_links);
			$user_bookings['start_date'] = $start_date;
			$user_bookings['end_date'] = $end_date;
			$user_bookings['dropoff_time'] = $dropoff_time;
			$user_bookings['pickup_time'] = $pickup_time;
			$user_bookings['color'] = $color;
			$booking_dates[] = $user_bookings;
		}
	}
	wp_reset_postdata();
	// Get the current date (if specified); default is null
	$current_date = isset($_GET['current_date']) ? $_GET['current_date'] : null;
	// Get the unique id (if specified); default is 0
	$uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
	// Alternative to the above, but using sessions instead
	// $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
	// Get the size (if specified); default is normal
	$size = isset($_GET['size']) ? $_GET['size'] : 'normal';
	// Create a new calendar instance
	$calendar = new Calendar($current_date, $uid, $size);
	//$calendar->add_event(1, 'Holiday', 'Going away for a while.', '2023-04-15 13:00:00', '2023-04-18 18:00:00', '#53ae6d');

	$i = 1;
	// Use this version to add a link to each dog within the modal popup, but remember that this causes the at a glance view to display a text based version of the url
//	foreach($booking_dates as $booking) {
//		$dog_links = array();
//		foreach($booking['dogs'] as $key => $dog_link) {
//			$dog_links[] = '<a href="'.$booking['dog_links'][$key].'">'.$booking['dogs'][$key].'</a>';
//		}
//		$calendar->add_event($i, $booking['service'].' for '.implode(', ', $dog_links), 'Ends on: ' . date('F j, Y', strtotime($booking['end_date'])) . ' @ ' . date('g:i a', strtotime($booking['pickup_time'])), $booking['start_date'] . ' ' . $booking['dropoff_time'], $booking['end_date'] . ' ' . $booking['pickup_time'], $booking['color']);
//		$i++;
//	}
//	return $calendar;
	
	//Use this as the standard version of the calendar (no links around dog names)
	foreach($booking_dates as $booking) {
		$calendar->add_event($i, $booking['dogs'], 'Ends on: ' . date('F j, Y', strtotime($booking['end_date'])) . ' @ ' . date('g:i a', strtotime($booking['pickup_time'])), $booking['start_date'] . ' ' . $booking['dropoff_time'], $booking['end_date'] . ' ' . $booking['pickup_time'], $booking['color']);
		$i++;
  }

  $blackout_dates = get_field( 'blackout_dates', 'option' );
  $date_col = array_column( $blackout_dates, 'blackout_date' );
  foreach( $date_col as $date ) {
    $calendar->add_event( $i, 'Blackout', 'Unavailable', $date . ' 00:00:00', $date . ' 23:59:59', '#AAA' );
    $i++;
  }
	return $calendar;
}

/* Hide Media library tab for non-admins */
function remove_medialibrary_tab($tabs) {
    if ( !current_user_can( 'administrator' ) ) {
        unset($tabs["mediaLibraryTitle"]);
    }
    return $tabs;
}
add_filter('media_view_strings', 'remove_medialibrary_tab');

// Redirect login page
//add_action('init','custom_login');
//function custom_login(){
//	global $pagenow;
//	if( 'wp-login.php' == $pagenow ) {
//		wp_redirect('/login');
//		exit();
//	}
//}

/* Hide Admin Bar for all non-admins */
if ( ! current_user_can( 'manage_options' ) ) {
	add_filter('show_admin_bar', '__return_false');
}

/* Hide media button and other things for users on the Leave a Review ACF Form */
add_filter( 'acf/get_valid_field', 'change_post_content_type');
function change_post_content_type( $field ) { 
    if($field['type'] == 'wysiwyg') {
        $field['tabs'] = 'visual';
        $field['toolbar'] = 'basic';
        $field['media_upload'] = 0;
    }
    return $field;
}

// Set default post title on Testimonials.
add_action( 'acf/save_post', 'save_testimonial_post', 10 );
function save_testimonial_post( $post_id )
{
  if ( get_post_type( $post_id ) == 'testimonial' )
  {
		$user = get_userdata( get_current_user_id() );
    $first = $user->first_name;
    $last = $user->last_name;
		$last_initial = substr( $last, 0, 1 );
		$user_avatar_url = get_wpupa_url($user->ID, ['size' => 'full']);
		$user_avatar_url = str_replace('i0.wp.com/','',$user_avatar_url);
		$explode = explode( '?', $user_avatar_url );
		$user_avatar_id = attachment_url_to_postid(array_shift( $explode ));
    
		$testimonial = get_post( $post_id );
    $testimonial->post_title = $first . ' ' . $last_initial .'.';
		set_post_thumbnail( $post_id, $user_avatar_id );
    $updated = wp_update_post( $testimonial );
  }
}

// Change the Label of the Post Title field on the Edit Dog Post Form
function my_acf_prepare_field( $field ) {
    $field['label'] = "Dog's Name";
    return $field;   
}
add_filter('acf/prepare_field/name=_post_title', 'my_acf_prepare_field');

// Update Dog Post featured image on save of Edit Dog Post Form
add_action('acfe/form/submit/post/form=update-dog', 'my_form_submit', 10, 5);
function my_form_submit($post_id, $type, $args, $form, $action){
    
    // retrieve my_image value
    $my_image = get_field('image');
    
    // set my_image ID as featured thumbnail for the newly created post
    set_post_thumbnail($post_id, $my_image['ID']);
    
}

// Update Dog Owner Field on Dog Post Creation from Add Dog(s) Form on  Edit Client Profile page
add_action( 'acf/save_post', 'update_owner', 10 );
function update_owner( $post_id ) {
	$user = get_userdata( get_current_user_id() );
	if(empty(get_field('field_649ef365034f9', $post_id))) {
		update_field('field_649ef365034f9', $user, $post_id);
	}
}

// Change order of posts on Dog Archive page to alpha sort
add_action( 'pre_get_posts', 'my_change_sort_order'); 
function my_change_sort_order($query){
		if(is_archive('dog')):
		 //If you wanted it for the archive of a custom post type use: is_post_type_archive( $post_type )
			 //Set the order ASC or DESC
			 $query->set( 'order', 'ASC' );
			 //Set the orderby
			 $query->set( 'orderby', 'title' );
			 $query->set('posts_per_page', -1);
		endif;    
};

/*
// Function for syncing to Google Calendar (will be triggered via cron)
if (!wp_next_scheduled ('sync_google_calendar')) {
  wp_schedule_event(time(), 'hourly', 'sync_google_calendar', array(), true);
}
add_action('sync_google_calendar', 'google_calendar_sync', 10); // Works with action being 'wp_loaded'

function google_calendar_sync() {
	$sync_url = home_url() . '/sync-to-google-calendar/';
	wp_remote_get($sync_url); 
}
 */

require get_template_directory() . '/class-bookings.php';

// On save, adds any *published* Booking posts to the Google calendar.  Also removes the event 
// if the post isn't published.
add_action( 'acf/save_post', 'save_booking' );
function save_booking( $post_id ) {
  if ( 'booking' == get_post_type( $post_id )) {
    $bookings = new Bookings;
    $events = $bookings->getGoogleEvents();

    // If this post ID already has a registered event, delete it first to ensure all information
    // is up to date.
    if ( ! empty( $event = $events['tdj-booking-'.$post_id] ) ) {
      $bookings->deleteGoogleEvent( $event->getId() );
    }

    $bookings->addGoogleEvent( $post_id );
  }
}

add_action( 'wp_trash_post', 'trash_booking', 10, 2 );
function trash_booking( $post_id, $previous_status ) {
  if ( 'booking' == get_post_type( $post_id )) {
    $bookings = new Bookings;
    $events = $bookings->getGoogleEvents();
    if ( ! empty( $event = $events['tdj-booking-'.$post_id] ) ) {
      $bookings->deleteGoogleEvent( $event->getId() );
    }
  }
}

/*
add_action( 'acf/delete_post', 'delete_booking' );
function delete_booking( $post_id ) {
  if ( 'booking' == get_post_type( $post_id )) {
    $bookings = new Bookings;
    if ( ! empty( $event = $events['tdj-booking-'.$post_id] ) ) {
      $bookings->deleteGoogleEvent( $event->getId() );
    }
  }
}
 */
