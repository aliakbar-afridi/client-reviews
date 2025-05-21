<?php
/**
 * Plugin Name: client_reviews
 * Plugin URI: https://example.com/
 * Description: A simple plugin to manage and display client reviews.
 * Version: 1.0.0
 * Author: Ali Akbar
 * Author URI: https://bawartech.com/
 * License: GPL2
 * Text Domain: client_reviews
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Hook into WordPress initialization
add_action('init', 'st_register_client_reviews_cpt');

function st_register_client_reviews_cpt()
{

    // Labels for the admin interface
    $labels = array(
        'name' => __('Client Reviews', 'client_reviews'),
        'singular_name' => __('Client Review', 'client_reviews'),
        'menu_name' => __('Client Reviews', 'client_reviews'),
        'name_admin_bar' => __('Client Review', 'client_reviews'),
        'add_new' => __('Add New', 'client_reviews'),
        'add_new_item' => __('Add New Client Review', 'client_reviews'),
        'new_item' => __('New Client Review', 'client_reviews'),
        'edit_item' => __('Edit Client Review', 'client_reviews'),
        'view_item' => __('View Client Review', 'client_reviews'),
        'all_items' => __('All Client Reviews', 'client_reviews'),
        'search_items' => __('Search Client Reviews', 'client_reviews'),
        'not_found' => __('No Client Reviews found.', 'client_reviews'),
        'not_found_in_trash' => __('No Client Reviews found in Trash.', 'client_reviews'),
    );

    // Arguments to define the post type behavior
    $args = array(
        'labels' => $labels, // Use labels defined above
        'public' => true, // Show on the front-end and admin
        'publicly_queryable' => true, // Allow queries like example.com/client_reviews/some-name
        'show_ui' => true, // Show in WP admin
        'show_in_menu' => true, // Show in the main admin menu
        'query_var' => true, // Enable query variable (e.g., ?client_reviews=abc)
        'rewrite' => array('slug' => 'client-reviews'), // URL structure
        'capability_type' => 'post', // Inherit permissions from 'post'
        'has_archive' => true, // Enable archive page
        'hierarchical' => false, // False = like posts, True = like pages
        'menu_position' => 20, // Position in admin menu
        'menu_icon' => 'dashicons-testimonial', // Admin menu icon
        'supports' => array(''), // Features enabled
        'show_in_rest' => true, // Enable Gutenberg support
    );

    // Register the post type
    register_post_type('client_reviews', $args);
}


// Add meta boxes
add_action('add_meta_boxes', 'wpr_add_client_reviews_metaboxes');

function wpr_add_client_reviews_metaboxes()
{
    add_meta_box(
        'wpr_client_reviews_details', // ID
        'client_reviews Details',    // Title
        'wpr_render_client_reviews_metabox', // Callback function
        'client_reviews',            // Post type
        'normal',                 // Context
        'high'                    // Priority
    );
}

// Render the meta box fields
function wpr_render_client_reviews_metabox($post)
{
    // Get saved values
    $client_review = get_post_meta($post->ID, '_wpr_client_review', true);
    $review_date = get_post_meta($post->ID, '_wpr_review_date', true);
    $client_name = get_post_meta($post->ID, '_wpr_client_name', true);
    $client_company = get_post_meta($post->ID, '_wpr_client_company', true);
    $client_image_url = get_post_meta($post->ID, '_wpr_client_image_url', true);
    $star_rating = get_post_meta($post->ID, '_wpr_star_rating', true);

    // Nonce field for security
    wp_nonce_field('wpr_save_client_reviews_meta', 'wpr_client_reviews_nonce');

    ?>
    <p>
        <label for="wpr_client_review"><strong>Client Review:</strong></label><br>
        <input type="text" name="wpr_client_review" id="wpr_client_review" value="<?php echo esc_attr($client_review); ?>"
            class="widefat" />
    </p>
    <p>
        <label for="wpr_review_date"><strong>Review Date:</strong></label><br>
        <input type="date" name="wpr_review_date" id="wpr_review_date" value="<?php echo esc_attr($review_date); ?>"
            class="widefat" />
    </p>
    <p>
        <label for="wpr_client_name"><strong>Client Name:</strong></label><br>
        <input type="text" name="wpr_client_name" id="wpr_client_name" value="<?php echo esc_attr($client_name); ?>"
            class="widefat" />
    </p>
    <p>
        <label for="wpr_client_company"><strong>Client Company:</strong></label><br>
        <input type="text" name="wpr_client_company" id="wpr_client_company"
            value="<?php echo esc_attr($client_company); ?>" class="widefat" />
    </p>
    <p>
        <label for="wpr_client_image_url"><strong>Client Image Url:</strong></label><br>
        <input type="url" name="wpr_client_image_url" id="wpr_client_image_url"
            value="<?php echo esc_attr($client_image_url); ?>" class="widefat" />
    </p>
    <p>
        <label for="wpr_star_rating"><strong>Star Rating (1-5):</strong></label><br>
        <select name="wpr_star_rating" id="wpr_star_rating" class="widefat">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>" <?php selected($star_rating, $i); ?>><?php echo $i; ?>
                    Star<?php echo $i > 1 ? 's' : ''; ?></option>
            <?php endfor; ?>
        </select>
    </p>
    <?php
}

// Save the custom field values
add_action('save_post', 'wpr_save_client_reviews_meta');

function wpr_save_client_reviews_meta($post_id)
{
    // Verify nonce
    if (!isset($_POST['wpr_client_reviews_nonce']) || !wp_verify_nonce($_POST['wpr_client_reviews_nonce'], 'wpr_save_client_reviews_meta')) {
        return;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permission
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save each field
    if (isset($_POST['wpr_client_review'])) {
        update_post_meta($post_id, '_wpr_client_review', sanitize_text_field($_POST['wpr_client_review']));
    }

    if (isset($_POST['wpr_review_date'])) {
        update_post_meta($post_id, '_wpr_review_date', sanitize_text_field($_POST['wpr_review_date']));
    }

    if (isset($_POST['wpr_client_name'])) {
        update_post_meta($post_id, '_wpr_client_name', sanitize_text_field($_POST['wpr_client_name']));
    }

    if (isset($_POST['wpr_client_company'])) {
        update_post_meta($post_id, '_wpr_client_company', sanitize_text_field($_POST['wpr_client_company']));
    }

    if (isset($_POST['wpr_client_image_url'])) {
        update_post_meta($post_id, '_wpr_client_image_url', esc_url_raw($_POST['wpr_client_image_url']));
    }

    if (isset($_POST['wpr_star_rating'])) {
        update_post_meta($post_id, '_wpr_star_rating', intval($_POST['wpr_star_rating']));
    }


    // Prevent infinite loop
    remove_action('save_post', 'wpr_save_client_reviews_meta');
    // Auto-Generate Post Title on Save 
    $author = get_post_meta($post_id, '_wpr_client_name', true); // fixed key
    $rating = get_post_meta($post_id, '_wpr_star_rating', true); // fixed key

    $title = "Review by {$author} - {$rating} stars";

    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => $title,
        'post_name' => sanitize_title($title)
    ));

    // Re-hook the function
    add_action('save_post', 'wpr_save_client_reviews_meta');

}


// Register the shortcode
add_shortcode('client_reviews', 'wpr_render_client_reviews_shortcode');

function wpr_render_client_reviews_shortcode($atts)
{
    // Fetch client_reviews
    $args = array(
        'post_type' => 'client_reviews',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    // If no client_reviews, return early
    if (!$query->have_posts()) {
        return '<p>No client_reviews found.</p>';
    }

    ob_start(); // Start capturing output

    echo '<div class="cr-client-reviews">';

    while ($query->have_posts()) {
        $query->the_post();

        // Get custom fields
        $client_review = get_post_meta(get_the_ID(), '_wpr_client_review', true);
        $client_image_url = get_post_meta(get_the_ID(), '_wpr_client_image_url', true);
        $client_name = get_post_meta(get_the_ID(), '_wpr_client_name', true);
        $client_company = get_post_meta(get_the_ID(), '_wpr_client_company', true);
        $star_rating = intval(get_post_meta(get_the_ID(), '_wpr_star_rating', true));
        $date = get_post_meta(get_the_ID(), '_wpr_review_date', true);
        $stars = str_repeat('⭐', $star_rating);

        ?>
        <div class="review-card">
            <div class="rating-date">
                <div class="stars"><?php echo $stars; ?></div>
                <div class="date"><?php echo $date; ?></div>
            </div>
            <p class="testimonial-text">
                <?php echo $client_review; ?>
            </p>
            <div class="author-info">
                <img src="<?php echo $client_image_url ?>" alt="<?php echo $client_name ?>" class="avatar">
                <div class="author-details">
                    <div class="name"><?php echo esc_html($client_name); ?></div>
                    <div class="position"><?php echo esc_html($client_company); ?></div>
                </div>
            </div>
        </div>
        <?php
    }

    echo '</div>';

    wp_reset_postdata(); // Reset global post object

    return ob_get_clean(); // Return captured output
}


// Enqueue frontend styles
add_action('wp_enqueue_scripts', 'st_enqueue_styles');

function st_enqueue_styles()
{
    wp_enqueue_style(
        'st-client_reviews-style',
        plugin_dir_url(__FILE__) . 'css/styles.css',
        array(),
        '1.0',
    );
}

// Add custom columns to the Testimonials admin list
add_filter('manage_client_reviews_posts_columns', 'wpr_add_client_reviews_columns');

function wpr_add_client_reviews_columns($columns)
{
    $columns['wpr_client_name'] = 'Client Name';
    $columns['wpr_client_company'] = 'Company';
    $columns['wpr_client_review'] = 'Review';
    $columns['wpr_star_rating'] = 'Rating';
    return $columns;
}

// Populate custom column content
add_action('manage_client_reviews_posts_custom_column', 'wpr_render_client_reviews_columns', 10, 2);

function wpr_render_client_reviews_columns($column, $post_id)
{
    if ($column == 'wpr_client_name') {
        echo esc_html(get_post_meta($post_id, '_wpr_client_name', true));
    }

    if ($column == 'wpr_client_company') {
        echo esc_html(get_post_meta($post_id, '_wpr_client_company', true));
    }

    if ($column == 'wpr_client_review') {
        echo esc_html(get_post_meta($post_id, '_wpr_client_review', true));
    }


    if ($column == 'wpr_star_rating') {
        $stars = intval(get_post_meta($post_id, '_wpr_star_rating', true));
        echo str_repeat('⭐', $stars);
    }
}

//  Hide Title & Editor via CSS (Optional UI Tweak)
function wpr_hide_title_editor_admin()
{
    global $post_type;
    if ($post_type === 'client_reviews') {
        echo '<style>#titlediv, #postdivrich { display: none !important; }</style>';
    }
}
add_action('admin_head', 'wpr_hide_title_editor_admin');