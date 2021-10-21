<?php
// Creating a cateegory, author and date archive which will dynamically pull in data appropiately
get_header(); 
pageBanner(array(
  'title' => 'All Programs',
  'subtitle' => 'There is something for everyone. Have a look around!'
));
?>

<div class="container container--narrow page-section">

<ul class="link-list min-list">

  <?php 
    while(have_posts()) {
        the_post(); ?>
        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
      <?php } 
      // Pagination
      echo paginate_links();
    ?>
</div>
</ul>

  <!-- Pagination -->

</div>

<?php get_footer(); ?>