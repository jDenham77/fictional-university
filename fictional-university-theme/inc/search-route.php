<?php 

function universityRegisterSearch() {
    // 3 Arguments: 1. Namespace (EX /wp-json/university/v1) 2. Route (ending part of url)
    register_rest_route('university/v1', 'search', array(
        // Methods are for crud, we could just put 'GET' but not all browsers will work with this 
        //  request so we use WP_REST_SERVER::READABLE
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'universitySearchResults'
    ));
}

// We pass in the data to this callback function and dynamically add the 's' search argument with $data
function universitySearchResults($data) {
    // This is valid php but not JS or JSON. WP handles and convers the php to json for us!!! Amazing!!
    //  This means we can use WP built in fucntions. So we write custom queries and let WP render it for us
    //  in a readable JSON syntax
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'), // This allows for the data from the last 10 professor posts to live in $professors
        's' => sanitize_text_field($data['term']) // withing this array we access what anyone appends to the url. Ex. /search?term=property
        // SECURITY: WP doesn't allow for someone to do a SQL injection in the search bar but we should also sanatize the search
    ));

    // Instead of returning all the JSON from WP_Query we create an empty array() and only store the applicable data
    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    // Loop through the collection and push the applicable data and custom fields to the $results[...]
    while($mainQuery->have_posts()) {
        $mainQuery->the_post();
        // 2 arguments: 1. array name 2. what's getting added on 
        if (get_post_type() == 'post' OR get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                // This is what we are adding with an associative array with WP functions
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        }

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                // This is what we are adding with an associative array with WP functions
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }

        if (get_post_type() == 'program') {
          $relatedCampuses = get_field('related_campus');

          if($relatedCampuses) {
            foreach($relatedCampuses as $campus) {
              array_push($results['campuses'], array(
                'title' => get_the_title($campus),
                'permalink' => get_the_permalink($campus)
              ));
            }
          }
            array_push($results['programs'], array(
                // This is what we are adding with an associative array with WP functions
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            ));
        }

        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                // This is what we are adding with an associative array with WP functions
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }

        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;

            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }

            array_push($results['events'], array(
                // This is what we are adding with an associative array with WP functions
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }

        
    }
// -------------------------------- RELATIONSHIP SEARCH QUERIES -------------------------------------------
  if ($results['programs']) {

    $programsMetaQuery = array('relation' => 'OR');
    
    foreach($results['programs'] as $item) {
        array_push($programsMetaQuery, array(
            // Key: ACF that we want to look in 
            'key' => 'related_programs',
            'compare' => 'LIKE',
            // Transfers the id of the post and makes it readable text
            'value' => '"' . $item['id'] . '"'
        ));
    }
    // This is the second search query for relationships. This creates a relationship between
    //   professors and programs and it says gives us a professor who teaches x subject.
    $programRelationshipQuery = new WP_Query(array(
        'post_type' => array('professor', 'event'),
        'meta_query' => $programsMetaQuery
    ));
    
    while($programRelationshipQuery->have_posts()) {
        $programRelationshipQuery->the_post();

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }

        if (get_post_type() == 'event') {
          $eventDate = new DateTime(get_field('event_date'));
          $description = null;

          if (has_excerpt()) {
              $description = get_the_excerpt();
          } else {
              $description = wp_trim_words(get_the_content(), 18);
          }

          array_push($results['events'], array(
              // This is what we are adding with an associative array with WP functions
              'title' => get_the_title(),
              'permalink' => get_the_permalink(),
              'month' => $eventDate->format('M'),
              'day' => $eventDate->format('d'),
              'description' => $description
          ));
      }
    }   
    // Removoes any duplicates getting sent to the REST API, but only for professors
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
   
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
  }    
    return $results;
}

add_action('rest_api_init', 'universityRegisterSearch');
