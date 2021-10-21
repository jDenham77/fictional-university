<?php get_header();

  while(have_posts()) {
    the_post(); 
    // We pass in arguments to control the title, subtitle, and background photo. We add this logic to functions.php 
    //  to be aware of these incomming arguments
    pageBanner(array(
      'title' => 'Hello there this is the title',
      'subtitle' => 'Hi, this is the subtitle',
      'photo' => 'https://yoast.com/app/uploads/2021/02/Copy-of-WordPress_FI-2.png'
    )); ?>

    <div class="container container--narrow page-section">

    <?php 
        $theParent = wp_get_post_parent_id(get_the_ID());
        // This function gives us the ID of the parent page. If no parent page zero is returned and if is false.
            if($theParent) { ?>
    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>">
                <i class="fa fa-home" aria-hidden="true"></i>
                Back to <?php echo get_the_title($theParent); ?></a> <span
                class="metabox__main"><?php the_title(); ?></span>
        </p>
    </div>
    <?php } ?>

    <?php
    // Tests to see if page is a parent
    $testArray = get_pages(array(
      'child_of' => get_the_ID()
    ));

    if($theParent or $testArray ) { ?>
    <div class="page-links">
        <h2 class="page-links__title">
            <a href="<?php echo get_permalink($theParent) ?>">
                <?php echo get_the_title($theParent); ?>
            </a>
        </h2>

        <ul class="min-list">
            <?php 
            if($theParent) {
              $findChildrenOf = $theParent;
            } else {
              $findChildrenOf = get_the_ID();
            }

            wp_list_pages(array(
              'title_li' => null,
              'child_of' => $findChildrenOf,
              'sort_column' => 'menu_order'
            ));
          ?>
        </ul>
    </div>
    <?php } ?>

    <div class="generic-content">
      <?php get_search_form(); ?>
    </div>
</div>

<?php }

        get_footer();
?>




