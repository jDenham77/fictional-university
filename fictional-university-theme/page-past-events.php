<?php
// Creating a cateegory, author and date archive which will dynamically pull in data appropiately
get_header(); 
pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'Recap of our past events.'
));
?>


<div class="container container--narrow page-section">
  <?php 

    $today = date('Ymd');
    $pastEvents = new WP_Query(array(
        //paged: tells custom query which paged number of results to be on. We get this from the url with the
        //  function get_query_var()
        'paged' => get_query_var('paged', 1),
        // 'posts_per_page' => 1,
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        // meta_query allows for filtering the data
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'compare' => '<',
                'value' => $today,
                'type' => 'numeric'
            )
        )
    ));

    while($pastEvents->have_posts()) {
        $pastEvents->the_post(); 
        get_template_part('template-parts/content-event');
      } 
      // Pagination: only works out the box with the default Queries WP makes that are tied to the URL. 
      // Pag works events archive not not here bec it's a custom query page so we tell it which page to get
      echo paginate_links(array(
          'total' => $pastEvents->max_num_pages
      ));
    ?>
</div>

  <!-- Pagination -->

</div>

<?php get_footer(); ?>