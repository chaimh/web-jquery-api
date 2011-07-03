<?php get_header() ?>
<!-- TEMPLATE PLUGIN CATEGORY -->

<?php
$sorted_list = array(
  '.tmpl()' => '',
  'jQuery.tmpl()' => '',
  '.tmplItem()' => '',
  'jQuery.tmplItem()' => '',
  '.template()' => '',
  'jQuery.template()' => '',
  '${} Template Tag' => '',
  '{{html}} Template Tag' => '',
  '{{if}} Template Tag' => '',
  '{{else}} Template Tag' => '',
  '{{each}} Template Tag' => '',
  '{{tmpl}} Template Tag' => '',
  '{{wrap}} Template Tag' => '',
);

?>
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

    foreach ($pageposts as $post):
      setup_postdata($post);

      $content = get_the_content();
      $tentry = '';
      $cats_meow = sandbox_cats_meow(', ');
      $betaflag = ks_beta_flag($post->ID, 'Banner');

      $tentry .= '<li id="post-' . $post->ID . '" class="keynav ' . sandbox_post_class(false) . '">';
        $tentry .= '<h2 class="entry-title">';
          $tentry .= '<a class="title-link" href="' . get_permalink() . '" rel="bookmark" title="' .  sprintf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) . '">';
            $tentry .= $post->post_title;
          $tentry .= '</a>';
        $tentry .= '</h2>';

        if ( $cats_meow || $betaflag ) :
          $cats = '<span class="entry-meta">';
            $cats .= $betaflag;
            $cats .= '<span class="cat-links">' . $cats_meow . '</span>';
          $cats .= '</span>';
          $tentry .= $cats;
        endif;

        $content = get_the_content();
          $nosig = preg_replace( "/<signature>.*?<\/signature>/s", "", $content );
          $match = array();
          preg_match( "/<desc>(.*?)<\/desc>/", $nosig, $match );
          $tentry .= "<p class='desc'>" .  $match[1] . "</p>";
      $tentry .= '</li>';


      $sorted_list[$post->post_title] = $tentry;

    endforeach;
    $sorted_posts = array_values($sorted_list);

    echo '<ul class="method-list">';
    echo implode("\n", $sorted_posts);
    echo '</ul>';

  endif;
?>

    </div><!-- #content -->
  </div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
