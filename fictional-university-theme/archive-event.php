<?php
// Creating a cateegory, author and date archive which will dynamically pull in data appropiately
get_header(); 
pageBanner(array(
  'title' => 'All Events',
  'subtitle' => 'See What is going on in our World.'
));
  ?>

<div class="container container--narrow page-section">
  <?php 
    while(have_posts()) {
        the_post();
        get_template_part('template-parts/content-event');
       } 
      // Pagination
      echo paginate_links();
    ?>

    <hr class="section-break" >

    <p>Looking for a recap of past events? 
      <a href="<?php echo site_url('/past-events') ?>">
        Check out our past events archive
      </a>.
    </p>

</div>

  <!-- Pagination -->

</div>

<?php get_footer(); ?>