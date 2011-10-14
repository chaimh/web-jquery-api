<?php get_header() ?>

  <div id="jq-primaryContent">
    <div id="content">

<?php the_post() ?>
<?php
$title = the_title('','', false);
$entry_class = sandbox_post_class(false);
$is_plugin = ks_in_plugins_category();
$entry_class .= $is_plugin ? ' plugin-entry' : '';
$reus_title = 'title=' . $post->post_title;

$banner_values = get_post_meta($post->ID, 'Banner');
$banner = '';
$banner_wrap = '';
if (!empty($banner_values) && function_exists('get_reus')) :
  $banner .= '<div class="banner">';
    $banner .= get_reus($banner_values[0], $reus_title);
  $banner .= '</div>';
  $banner_wrap = ' b-wrap';
endif;
?>
      <div id="post-<?php the_ID() ?>" class="<?php echo $entry_class; ?>">
        <div class="entry-content<?= $banner_wrap; ?>">
        <div class="entry-title roundTop">
          <?php if ($is_plugin): ?>
          <h2 class="plugin-header">jQuery Plugin</h2>
          <?php endif; ?>

          <h1 class="jq-clearfix"><?php echo $title; ?></h1>
          <div class="entry-meta jq-clearfix">
            <?php echo $banner;  ?>
            Categories:
            <?php
            $all_cats = get_categories();
            $cat_list = array();
            foreach ($all_cats as $cat => $catinfo) {
              $catid = $catinfo->term_id;
              if (in_category( $catid ) && strpos($catinfo->cat_name, "Version") === false) {
                $cat_list[] = '<span class="category">' . substr(get_category_parents($catid, true, ' &gt; '), 0, -6) . '</span>';
              }
            }
            echo implode(' | ', $cat_list);

            ?>

  <?php edit_post_link( __( 'Edit', 'sandbox' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" ); ?>


          </div>

</div>
<?php

$xp = new XsltProcessor();

// create a DOM document and load the XSL stylesheet
$xsl = new DomDocument;
$xsl->load(TEMPLATEPATH.'/entries2html.xsl');

// import the XSL styelsheet into the XSLT process
$xp->importStylesheet($xsl);

// create a DOM document and load the XML data
$xml_doc = new DomDocument;
$xml_doc->loadXML(get_the_content());

// transform the XML into HTML using the XSL file
if ($html = $xp->transformToXML($xml_doc)) {
  $html = preg_replace('@</?html>@', '', $html);

  $htmlparts = explode('<h3>Example', $html);
  $firstpart = array_shift($htmlparts);

  $meta_values = get_post_meta($post->ID, 'Notemeta');
  $note_values = get_post_meta($post->ID, 'Note');
  $reus_vars = $reus_title;
  $notes = '';
  if (!empty($note_values) && function_exists('get_reus')) :

    if ( !empty($meta_values) ):
      foreach ($meta_values as $meta) {
        $meta_parts = explode('=', $meta);
        $meta_key = array_shift($meta_parts);
        $meta_val = implode('=', $meta_parts);
        $meta = $meta_key . '=' . str_replace('=', '%3D', $meta_val);

        $reus_vars .= '&' . $meta;
      }
    endif;

    $notes .= '<ul>';
    foreach ($note_values as $value) {
      $note = get_reus($value, $reus_vars);
      $note = str_replace('%3D', '=', $note);
      $notes .= '<li>' .  $note . '</li>';
    }
    $notes .= '</ul>';
  endif;


  // print out the html ...

  echo $firstpart;

  if ( count($htmlparts) ) :
    for ($i=0; $i < count($htmlparts); $i++) {
      if ($notes) :
        echo '<h3 id="notes-' . $i . '">Additional Notes:</h3>';
        echo '<div class="longdesc">' . $notes . '</div>';

      endif;
      echo '<h3>Example' . $htmlparts[$i];
    }

  endif;

} else {
  trigger_error('XSL transformation failed.', E_USER_ERROR);
} // if

?>

<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'sandbox' ) . '&after=</div>') ?>
        </div>
      </div><!-- .post -->
    <?php
      // show prev/next links only for Karl so he can finish editing posts
      ks_prev_next('karl.swedberg');
    ?>
<h1 id="comments" class="roundTop section-title">Support and Contributions</h1>
<div class="jq-box roundBottom">
  <p>Need help with <strong><?= the_title(); ?></strong> or have a question about it? Visit the <a href="http://forum.jquery.com/">jQuery Forum</a> or the <strong>#jquery</strong> channel on <a href="irc://irc.freenode.net/">irc.freenode.net</a>.</p>
  <p>Think you've discovered a jQuery bug related to <strong><?= the_title(); ?></strong>? <a href="http://docs.jquery.com/How_to_Report_Bugs">Report it</a> to the jQuery core team.</p>

  <?php
  $apireport = ks_api_report($_POST);
  switch ( $apireport['status'] ) {
    case 'unsent':
      $preform = '<p>Found a problem with this documentation? <a id="api-error" href="#rpt-issue">Report it</a> to the jQuery API team.</p>';
      $postform = <<<SCRPT
        <script>jQuery(function($) { $('#rpt-issue').hide();$('#api-error').bind('click',function(e) {e.preventDefault();$('#rpt-issue').slideDown().find('button').prop('disabled', false);});});</script>
SCRPT;
      $showform = true;
      break;

    case 'error':
      $showform = true;
      break;
    default:
      $preform = '';
      $showform = false;
      $postform = '';
      break;
  }

  if ( !$showform ):
    echo $apireport['msg'];
  else:
    $errs = $apireport['errors'];

    echo $preform;
  ?>
    <form id="rpt-issue" action="#comments" method="post">
      <?php
      echo $apireport['msg'];
      ?>
      <fieldset>
        <div style="position:absolute;left:-1000em;">
          <input type="text" name="address" value="">
          <input type="hidden" name="date" value="<?= date('d M Y'); ?>">
        </div>
        <div<?php ks_err('fullname',$errs); ?>>
          <label for="api_name">Name:</label>
          <input name="fullname" id="api_name" value="<?= ks_field_value('fullname'); ?>">
        </div>
        <div<?php ks_err('email',$errs); ?>>
          <label for="api_email">Email Address:</label>
          <input name="email" id="api_email" value="<?= ks_field_value('email'); ?>">
        </div>
        <div<?php ks_err('api_title',$errs); ?>>
          <label for="api_title">Subject:</label>
          <input id="api_title" name="api_title" value="Documentation problem with <?= the_title(); ?>" />
        </div>
        <div<?php ks_err('api_comment',$errs); ?>>
          <label for="api_comment">Comment:</label>
          <textarea id="api_comment" name="api_comment"><?= ks_field_value('api_comment'); ?></textarea>
        </div>
        <button type="submit" disabled="disabled">Report</button>
      </fieldset>
    </form>

  <?php
    echo $postform;
  endif;

  ?>
  <!-- <ul class="comment-instructions">
    <li><h3 style="margin-top: 0; font-size: 1.4em">Support requests, bug reports, and off-topic comments will be <em>deleted</em> without warning.</h3></li>
    <li>Please do post corrections or additional examples for <?php echo $title; ?> below. We aim to quickly move corrections into the documentation.</li>
    <li>If you need help, post at the <a href="http://forum.jquery.com/">forums</a> or in the #jquery IRC channel.</li>
    <li>Report bugs on the <a href="http://dev.jquery.com/newticket/">bug tracker</a> or the <a href="http://forum.jquery.com/developing-jquery-core">jQuery Forum</a>.</li>
    <li>Discussions about the API specifically should be addressed in the <a href="http://forum.jquery.com/developing-jquery-core">Developing jQuery Core forum</a>.</li>
  </ul> -->
<div style="margin-top:2em;">
<?php
comments_template();
?>
</div>
</div>
    </div><!-- #content -->
  </div><!-- #jq-primaryContent -->

<?php get_sidebar() ?>
<?php get_footer() ?>
