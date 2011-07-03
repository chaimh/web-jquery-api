<?php get_header() ?>

	<div id="jq-primaryContent">
		<div id="content">

<?php the_post() ?>
			<div id="post-<?php the_ID() ?>" class="entry <?php sandbox_post_class() ?>">
  			<div class="entry-content">
  			  <div class="entry-title roundTop">

            <h1 class="jq-clearfix" style="border-bottom-width: 0"><?php the_title() ?></h1>
            <div class="entry-meta"></div>
          </div>
          <div class="entry jq-box">
            <?php the_content() ?>
            <?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'sandbox' ) . '&after=</div>') ?>
            <?php edit_post_link( __( 'Edit', 'sandbox' ), '<span class="edit-link">', '</span>' ) ?>
          </div>
			  </div><?php // #post... ?>
		  </div><!-- .entry-content -->

<?php if ( get_post_custom_values('comments') ) comments_template() // Add a key+value of "comments" to enable comments on this page ?>

		</div><!-- #content -->
	</div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>