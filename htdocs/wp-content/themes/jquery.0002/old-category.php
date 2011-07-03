<?php get_header() ?>

	<div id="jq-primaryContent" class="jq-box round">
  	<h1 class="page-title"><span><?php single_cat_title() ?>:</span></h1>
		<div id="content">
			<?php $categorydesc = category_description(); if ( !empty($categorydesc) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' ); ?>

<ul id="method-list">

<?php
query_posts($query_string.'&posts_per_page=-1&orderby=title&order=asc');
while ( have_posts() ) : the_post() ?>

			<li class="keynav" id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?>">
				<h2 class="entry-title"><a class="title-link" href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'sandbox' ), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<span class="entry-meta">
<?php if ( $cats_meow = sandbox_cats_meow(', ') ) : // Returns categories other than the one queried ?>
					<span class="cat-links"><?php printf( __( 'Also in %s', 'sandbox' ), $cats_meow ) ?></span>
<?php endif ?>
  <?php edit_post_link( __( 'Edit', 'sandbox' ), "\t\t\t\t\t  | <span class=\"edit-link\">", "</span>\n" ) ?>
				</span>
        <?php 
        $nosig = preg_replace( "/<signature>.*?<\/signature>/s", "", get_the_content() );
        $match = array();
        preg_match( "/<desc>(.*?)<\/desc>/", $nosig, $match );
        echo "<p class='desc'>" . $match[1] . "</p>";
        ?>

			</li><!-- .post -->

<?php endwhile; ?>
</ul>
		</div><!-- #content -->
	</div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
