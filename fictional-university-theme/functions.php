<?php 

  require get_theme_file_path('/inc/like-route.php');
  require get_theme_file_path('/inc/search-route.php');
  // Adds new REST fields to the API
  function university_custom_rest() {
    // Inside her we can customize rest api with register_rest_field
    // 3 arguments: 1. Post type 2. name of field 3. array of how we create this field
    register_rest_field('post', 'authorName', array(
      'get_callback' => function() { return get_the_author(); }
    ));
    // Adds user note count to the API so we can use it on the front end 
    register_rest_field('note', 'userNoteCount', array(
      'get_callback' => function() { return count_user_posts(get_current_user_id(), 'note'); }
    ));
  }


  add_action('rest_api_init', 'university_custom_rest');

// We can set the arguments to null incase nothing is passed in we wont get an error
    function pageBanner($args = NULL) {
      // This is a fallback logic for the title, either get the supplied arguments from pageBanner Array('title', 'subtitle', 'backgroundPhoto')
      //  or display WP Post Page title.
      if (!$args['title']) {
        $args['title'] = get_the_title();
      }
     
      if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
      }
    // Checks if a photo was supplied first, then checks ACF photo and if it's not archive and not home url, 
    // else it will return a default ocean image
      if (!$args['photo']) {
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
          $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
          $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
      }

    ?>
    
    <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(
        <?php echo $args['photo']; ?>)" >
      </div>

      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
        <div class="page-banner__intro">
          <p>
            <?php echo $args['subtitle']; ?>
          </p>
        </div>
      </div>
    </div>

<?php  }  

// wp_enqueue_scripts is used to tell WP I want to load CSS, JS in header or footer
function university_files() {
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyA8Cw9nqb37B3oCo2G-QMcAkI34GBb4iRQ', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom_google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
    // wp_localize_script: WP function allows us to output javascript data to the html source of the webpage, We can make the root URL
    //    dynamic so that it wont get messed up in different environments
    // 3 Arguments: 1. Name or handle of JS file making it flexible 2. Variable Name ***3. Array of data we want available in JS 
    wp_localize_script('main-university-js', 'universityData', array(
      'root_url' => get_site_url(),  // Used to find the root url throughout the app
      'nonce' => wp_create_nonce('wp_rest') // Nonce: number used once for a user session
    ));
}

add_action('wp_enqueue_scripts', 'university_files');


function university_features() {
    /* (register_nav_manu) allows for menus to be controlled inside admin dashboard
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocationOne', 'Footer Location One');
    register_nav_menu('footerLocationTwo', 'Footer Location Two'); */
    add_theme_support('title-tag'); 
    add_theme_support('post-thumbnails');
    // Resize thumbnail images. Aguments: name, width(px), heigth(px), crop
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');
//Adds new menu locations to a theme

function university_adjust_queries($query) {
    //Action is used to manipulate the data from the posts before we get the query.
    // We only manipulate the query if we are not backend admin, on event post type archive, and it's a main query.
    
    // Custom query where we override WP default 10 posts per page, instead we use them all with -1
    if(!is_admin() AND is_post_type_archive('campus') AND is_main_query()) {
      $query->set('posts_per_page', -1);
  }
    
    if(!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

    // Customizing Event Archive Query
    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        // meta_query used to filter data
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');


function university_map_key($api) {
  $api['key'] = 'AIzaSyA8Cw9nqb37B3oCo2G-QMcAkI34GBb4iRQ';
  return $api;
}

add_filter('acf/fields/google_map/api', 'university_map_key');


// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    // exit tells php to stop
    exit;
  }
}

add_action('wp_loaded', 'noSubsAdminBar');
// Removes admin bar when logged in 
function noSubsAdminBar() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

// Customize Login Screen
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
  // Change image url from login screen
  return esc_url(site_url('/'));
}


// load own css on login page. to to CSS modules login.css and make changes
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('custom_google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}


// Remove Powered By Wordpress on login page
add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
  return get_bloginfo('name');
}

// Force note posts to be private SERVER SIDE not CLIENT because that can be modified
// 4 arguments: 1.wp insert post data; 2. function name; 3. order to execute, 4. parameters passed (10 default)
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) {
  if ($data['post_type'] == 'note') {
    if (count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
      die("You have reached your note limit."); // This shows up in the request.responseText
    }
  // *** This will escape the html from the notes being added to the DB. Default WP allows 
  //  unfiltered HTML for admin only but allows for html. In the front end it wil be shown as
  //  raw html but can be viewed as visual. 
    // sanitize_textarea_field wont allow HTML to be posted to DB
    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }
  // force the notes post to equal private so that it's not shown in the WP REST API
  if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
    $data['post_status'] = "private";
  }
  // The data that's exiting our function aka filtered
  return $data;
}