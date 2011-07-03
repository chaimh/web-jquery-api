<?php get_header() ?>

	<div id="jq-primaryContent" class="jq-box round">
  	<h1 class="page-title"><?php single_cat_title() ?></h1>
		<div id="content">
			<?php
			if (function_exists('ks_term_description')) :
			  $categorydesc = ks_term_description();
			else :
			  $categorydesc = category_description();
			endif;

			if ( !empty($categorydesc) ) {
			  echo apply_filters( 'archive_meta', '<br/><div class="archive-meta">' . $categorydesc . '</div>' );
			}
			?>

  <?php
  $plugins_cat = ks_plugins_category();
  if ( is_category($plugins_cat) ) :
    echo '<ul class="plugin-name-list">';
    wp_list_categories('title_li=&depth=1&child_of=' . $plugins_cat);


  else:
    echo '<ul id="method-list" class="method-list">';
  ?>

  <?php
  $pageposts = $wpdb->get_results("SELECT *, REPLACE(post_name, 'jQuery.', '') as trimname FROM wp_posts as p, wp_term_taxonomy as tt, wp_term_relationships as t WHERE p.post_status='publish' AND p.post_type = 'post' AND p.post_date < NOW() AND (tt.term_taxonomy_id=$cat AND t.term_taxonomy_id=$cat OR tt.parent=$cat AND t.term_taxonomy_id=tt.term_taxonomy_id) AND t.object_id=p.ID ORDER BY trimname;");
  ?>

  <?php if ($pageposts): ?>
  <?php $postlist = array(); ?>
  <?php foreach ($pageposts as $post): ?>
  <?php setup_postdata($post); ?>
    <?php
    if ( in_array($post->ID, $postlist) ) { continue;}
    $postlist[] = $post->ID;
    ?>
  			<li id="post-<?php the_ID() ?>" class="keynav <?php sandbox_post_class() ?>">
  				<h2 class="entry-title">
            <a class="title-link" href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title(); ?></a></h2>
    <?php
    $betaflag = ks_beta_flag($post->ID, 'Banner');
    $close_meta = '';
    if ( $cats_meow = sandbox_cats_meow(', ') || $betaflag ) : // Returns categories other than the one queried
      echo '<span class="entry-meta">';
      echo $betaflag;
      $close_meta = '</span>';
    endif;
    if ( $cats_meow = sandbox_cats_meow(', ') ) :

    	$content = get_the_content();
    	$isnew = true;
    	$category = get_the_category_by_ID($cat);
    	$pos = strpos( $category, "Version" );

    	if ( $pos !== false ) {
    		$version = substr( $category, 8 );
    		$matches = array();

    		preg_match_all("/<added>([^<]+)</", $content, $matches);

    		foreach ( $matches[1] as $match ) {
    			if ( $match < $version ) {
    				$isnew = false;
    				break;
    			}
    		}

    		if ( $isnew ) {
    			echo "<span class='new' style='background-color:#0D5995;color:#fff;padding:3px;margin-right:2px;position:relative;left:-4px;'>New in $version!</span>";
    		}
    	}
      ?>
      <span class="cat-links"><?php printf( __( '%s', 'sandbox' ), $cats_meow ) ?></span>
    <?php
      edit_post_link( __( 'Edit', 'sandbox' ), "\t\t\t\t\t  | <span class=\"edit-link\">", "</span>\n" );
    endif;
    echo $close_meta;

  	    $content = get_the_content();
          $nosig = preg_replace( "/<signature>.*?<\/signature>/s", "", $content );
          $match = array();
          preg_match( "/<desc>(.*?)<\/desc>/", $nosig, $match );
          echo "<p class='desc'>" . $match[1] . "</p>";
          ?>
  			</li><!-- .post -->

      <?php endforeach; ?>
    <?php endif; // end if not the plugins cat ?>
  </ul>
<?php endif; ?>
		</div><!-- #content -->
	</div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
