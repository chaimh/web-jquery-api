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

$pageposts = $wpdb->get_results("SELECT *, REPLACE(post_name, 'jQuery.', '') as trimname FROM wp_posts as p, wp_term_taxonomy as tt, wp_term_relationships as t WHERE p.post_status='publish' AND p.post_type = 'post' AND p.post_date < NOW() AND (tt.term_taxonomy_id=$cat AND t.term_taxonomy_id=$cat OR tt.parent=$cat AND t.term_taxonomy_id=tt.term_taxonomy_id) AND t.object_id=p.ID ORDER BY trimname;");

if ($pageposts):
  $catlist = array();
  $versionlist = array();
  $postlist = array();

  foreach ($pageposts as $post):
    setup_postdata($post);
    if ( in_array($post->ID, $postlist) ) { continue;}
    $postlist[] = $post->ID;

    $post_start = '<li id="post-' . get_the_ID() . '" class="keynav">';
    $post_start .= '<h2 class="entry-title">';
    $post_start .= '<a class="title-link" href="' . get_permalink() . '" title="' . sprintf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) . '" rel="bookmark">' . $post->post_title . '</a></h2>';

    $content = get_the_content();
    $nosig = preg_replace( "/<signature>.*?<\/signature>/s", "", $content );
    $match = array();
    preg_match( "/<desc>(.*?)<\/desc>/", $nosig, $match );
    $post_end = '<p class="desc">' . $match[1] . '</p></li>';
    if ( $cats_meow = sandbox_cats_meow(', ') ) : // Returns categories other than the one queried
      $othercats = '<span class="entry-meta">';

      $content = get_the_content();
      $category = get_the_category_by_ID($cat);
      $pos = strpos( $category, "Version" );

      $othercats = '<span class="entry-meta"><span class="cat-links">' . sprintf( __( '%s', 'sandbox' ), $cats_meow ) . '</span>';
      $othercats .= ' | <span><a href="' . get_edit_post_link( $post->ID)  . '">Edit</a></span>';
      $othercats .= '</span></span>';

    	if ( $pos !== false ) {
        $matches = array();
        preg_match_all("/<added>([^<]+)</", $content, $matches);
        $maxVersion = max($matches[1]);

        if (!isset($catlist[$maxVersion])):
          $catlist[$maxVersion] = array();
        endif;
        $catlist[$maxVersion][] = $post_start . $othercats . $post_end;
      }
    endif;
  endforeach;
  ?>
  <ul id="method-list" class="method-list">
  <?php
  krsort($catlist);

  foreach ($catlist as $key => $value) {
    echo '<li class="version-heading"><h2>Last Added/Updated in <a href="/category/version/' . $key . '/">Version ' . $key . '</a>:</h2></li>';
    echo implode("\n", $value);
  }
  ?>
  </ul>
  <?php
endif; ?>
  </div><!-- #content -->
</div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
