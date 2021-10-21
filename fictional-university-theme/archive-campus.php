<?php
// Creating a cateegory, author and date archive which will dynamically pull in data appropiately
get_header(); 
pageBanner(array(
  'title' => 'Our Campuses',
  'subtitle' => 'We have several conviently located Campuses'
));
?>

<div class="container container--narrow page-section">
    <div class="acf-map">
    <?php 
        while(have_posts()) {
            the_post(); 
            // Create a variable to store acf data of map location.  
            $mapLocation = get_field('map_location');
            ?>
            <!-- This is where Google Maps displays the data -->
            <div class="marker" 
                data-lat="<?php echo $mapLocation['lat']; ?>" 
                data-lng="<?php echo $mapLocation['lng']; ?>">
            <!-- Inside this div is where google looks for any pop up information -->
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php echo $mapLocation['address']; ?>
            </div>
        <?php } ?>
    </div>
</div>

  <!-- Pagination -->

</div>

<?php get_footer(); ?>