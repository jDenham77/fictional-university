<?php 
// like-route.php is a file that's imported into functions.php to keep things organized

// Add a new custom route or to add a new field to a route!!!
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
  // register_rest_route(a, b, c) a: namespace; b: name for route; c: array(methods and callback)
  // we then create ajax call with endpoint manageLiuke in Like.js
  register_rest_route('university/v1', 'manageLike', array(
      'methods' => 'POST',
      'callback' => 'createLike'
  ));

  register_rest_route('university/v1', 'manageLike', array(
      'methods' => 'DELETE',
      'callback' => 'deleteLike'
  ));
}
// register_rest_route passes data into the callback function
function createLike($data) {
  if (is_user_logged_in()) {
      // $data is from the axios request inside Like.js being sent to the server
      $professor = sanitize_text_field($data['professorId']); 

      $existQuery = new WP_query(array(
          'author' => get_current_user_id(), 
          'post_type' => 'like',
          'meta_query' => array(
            array(
              'key' => 'liked_professor_id',
              'compare' => '=',
              'value' => $professor
            )
          )
        ));
      
      if ($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor') {
          //create new like post
          // wp_insert_post() lets us create a post right from the php code and can return values like ID
          return wp_insert_post(array(
              'post_type' => 'like',
              'post_status' => 'publish',
              'post_title' => '2nd PHP test',
              // *** This will create native WP custom fields OR META FIELDS. 'key' => 'value'
              // This will pass the data to the ACF liked_professor_type inside the Like Post
              'meta_input' => array(
                  // since we return the wp_insert_post we are logging the professor ID as well
                  'liked_professor_id' => $professor
              )
          ));
      } else {
        die('Invalid Professor ID');
      }

  } else {
      // Kills the server to save resources and logs this message
      die("Only logged in users can create a like.");
  }
}

function deleteLike($data) {
  // Id of post to delete passed from register_rest_route
  $likeId = sanitize_text_field($data['like']);
  // wp_delete_post(id of post, bool for trash perminate delete. true = skip trash)
  if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true);
    return 'Congrats, like deleted.';
  } else {
    die("You do not have permission to delete that.");
  }
}
