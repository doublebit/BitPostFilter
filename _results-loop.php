<?php

/**
 * The basic loop of the posts
 */

// start the output buffer. DO NOT TOUCH
ob_start();

// loops start here
// you CAN edit below

if (have_posts()) {

while (have_posts()) : the_post();
    // edit individual post below
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-article'); ?>>

    <div
        class="entry-content">

        <a href="<?php the_permalink(get_the_ID()); ?>"><?php the_title(); ?></a>
        <?php the_content(); ?>

    </div>
    <div class="clearboth"></div>

</article>
<!-- #post-<?php get_the_ID(); ?> -->

<?php
endwhile;

} else {
    echo '<h3>No results!</h3>';
}

// loop ends here. DO NOT EDIT BELOW
// output or return the buffer, based on display type
$output = ob_get_contents();
ob_end_clean();
if (isset($ob) && $ob == true) {
    return $output;
} else {
    echo $output;
}
