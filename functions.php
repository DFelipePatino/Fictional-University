<?php

/**
 * Load environment variables from .env file in WordPress root
 */
function load_env_file()
{
    static $env_loaded = false;

    if ($env_loaded) {
        return;
    }

    // Path to .env file in WordPress root (two levels up from theme directory)
    $env_path = ABSPATH . '.env';

    if (file_exists($env_path)) {
        // FILE_IGNORE_NEW_LINES (2) | FILE_SKIP_EMPTY_LINES (4)
        $lines = @file($env_path, 2 | 4);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                // Set environment variable if not already set
                if (!getenv($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }

    $env_loaded = true;
}

// Load .env file early
load_env_file();

/**
 * Get Google Maps API key from environment variables
 */
function get_google_maps_api_key()
{
    // Check environment variable first
    $key = getenv('GOOGLE_MAPS_API_KEY');

    // Fallback to $_ENV superglobal
    if (empty($key) && isset($_ENV['GOOGLE_MAPS_API_KEY'])) {
        $key = $_ENV['GOOGLE_MAPS_API_KEY'];
    }

    // Fallback to WordPress constant (can be set in wp-config.php)
    if (empty($key) && defined('GOOGLE_MAPS_API_KEY')) {
        $key = constant('GOOGLE_MAPS_API_KEY');
    }

    // Development fallback (remove in production)
    if (empty($key)) {
        $key = 'AIzaSyBmRLaursZs_olbDhYmWWN3jHdhuRhvXbc';
    }

    return $key;
}

function pageBanner($args = NULL)
{

    if (!isset($args['title'])) {
        $args['title'] = get_the_title();
    }

    if (!isset($args['subtitle'])) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!isset($args['photo'])) {
        if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php }

function university_files()
{
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=' . get_google_maps_api_key(), NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url()
    ));
}


add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocationOne', 'Footer Location One');
    register_nav_menu('footerLocationTwo', 'Footer Location Two');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}


add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query)
{

    if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
        $query->set('posts_per_page', '-1');
    }

    if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', '-1');
    }


    if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                "compare" => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');

function universityMapKey($api)
{
    $api['key'] = get_google_maps_api_key();
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');



function my_get_all_post_types()
{
    // Get all post types as objects
    $post_types = get_post_types(
        array(),      // no filtering — get everything
        'objects'     // return as objects, not strings
    );

    return $post_types;
}
