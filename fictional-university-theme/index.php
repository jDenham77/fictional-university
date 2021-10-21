<!-- Blog Homekpage -->

<?php

get_header(); 
pageBanner(array(
  'title' => 'Welcome To Our Blog',
  'subtitle' => 'Keep Up With Our Lastest News!'
));

?>

<div class="container container--narrow page-section">
  <?php 
    while(have_posts()) {
      the_post(); ?>
      <div class="post-item">
        <h2>
          <a class="headline headline--medium headline--post-title" href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
          </a>
        </h2>

        <!-- This creates the Name and Category link to the Authors posts or Categories  -->
        <div class="metabox">
          <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?>in <?php echo get_the_category_list(', ') ?></p>
        </div>
        <!-- Excerpt -->
        <div class="generic-content">
          <?php the_excerpt(); ?>
          <p>
          <a class="btn btn--blue"href="<?php the_permalink(); ?>">
            Continue Reading
          </a>
          </p>
        </div>
    </div>
  <?php } 
    // Pagination
    echo paginate_links();
  ?>

  <!-- Pagination -->

</div>

<?php get_footer(); ?>