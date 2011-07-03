<?php get_header() ?>

  <div id="jq-primaryContent" class="jq-box round">
    <div id="content">

<?php if ( have_posts() ) : ?>
    <h1 class="page-title"><?php _e( 'Search Results for', 'sandbox' ) ?>
      <i><?php the_search_query() ?></i>
    </h1>
<?php
$searchquery = get_search_query();
$searchquery = preg_replace('@\$@', 'jQuery', $searchquery);
$featuredlist = array();
$entrylist = array();
$pluginlist = array();
$desc = array();

?>

<?php
while ( have_posts() ) :
  the_post();
  $fullentry = preg_replace( '@<example.*</example>@s', '', get_the_content() );
  $nosig = preg_replace( "@<signature>.*?<\/signature>@s", "", $fullentry );
  preg_match( "@<desc>(.*?)</desc>@s", $nosig, $desc );
  $desc = $desc[1];
  $fullentry = strip_tags($fullentry);
  $isplugin = ks_in_plugins_category();
  $itemclass = $isplugin ? 'plugin' : 'core';

  $tentry = '<li id="post-' . $post->ID . '" class="' . $itemclass . '">';
    $tentry .= '<h2 class="entry-title">';
      $tentry .= '<a class="title-link" href="' . get_permalink() . '" rel="bookmark" title="' .  sprintf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) . '">';
        $tentry .= $post->post_title;
      $tentry .= '</a>';
    $tentry .= '</h2>';
    $tentry .= '<span class="entry-meta">';

    if ( $cats_meow = sandbox_cats_meow(', ') ) :
      $tentry .= '<span class="cat-links">' . sprintf( __( '%s', 'sandbox' ), $cats_meow ) . '</span>';
    endif;
    $tentry .= '</span>';

    $tentry .= '<p class="desc">' . $desc . '</p>';
  $tentry .= '</li>';

  if ( $isplugin ) :
    $pluginlist[$post->post_name] = $tentry;
  elseif (preg_match('@' . $searchquery . '@i', $post->post_name)) :
    $featuredlist[$post->post_name] = $tentry;
  else :
    $entrylist[$post->post_name] = $tentry;
  endif;

endwhile; ?>

  <ul id="method-list" class="method-list">
    <?php
    echo implode("\n", $featuredlist);
    ksort($entrylist);
    echo implode("\n", $entrylist);
    ?>
  </ul>

  <?php if ( count($pluginlist) && ks_can_show_plugins() ) : ?>
    <h2 class="plugin-list-hdr">Plugin Search Results for <i><?php the_search_query() ?></i></h2>
    <ul id="plugin-list" class="method-list">
      <?php
      echo implode("\n", $pluginlist);
      ?>
    </ul>
  <?php endif; ?>

<?php else : ?>

      <div id="post-0" class="post no-results not-found">
        <h2 class="entry-title"><?php _e( 'Nothing Found', 'sandbox' ) ?></h2>
        <div class="entry-content">
          <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'sandbox' ) ?></p>
        </div>
        <form id="searchform-no-results" class="blog-search" method="get" action="<?php bloginfo('home') ?>">
          <div>
            <input id="s-no-results" name="s" class="text" type="text" value="<?php the_search_query() ?>" size="40" />
            <input class="button" type="submit" value="<?php _e( 'Find', 'sandbox' ) ?>" />
          </div>
        </form>
      </div><!-- .post -->

<?php endif; ?>

    </div><!-- #content -->
  </div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
