  <div id="jq-interiorNavigation" >
    <h2 class="jq-clearfix roundTop section-title"><a href="/">jQuery API</a></h2>
    <div class="roundBottom jq-box">
      <ul class="xoxo">
        <li>
          <ul>
            <li><a href="http://api.jquery.com/category/version/1.6/">New or Changed in 1.6</a></li>
            <li><a href="http://api.jquery.com/api/" >Raw XML API Dump</a></li>
            <li><a href="http://api.jquery.com/browser/">Dynamic API Browser</a></li>
            <li><a href="http://www.packtpub.com/jquery-1-4-reference-guide/book">jQuery API Book</a></li>
          </ul>
<?php if ( !is_single() ): ?>
          <div class="round notice" style="color:#333;padding:6px;background:#EEE;margin-top:5px;">Keyboard navigation now available! Use up, down, tab, shift+tab, shift+upArrow and enter to navigate.</div>
        </li>
<?php endif; ?>
      </ul>
<!--
      <br />
      <div class="sideBarFeature">
        <strong>Dynamic API Browser</strong>
        <a href="http://api.jquery.com/browser/" style="border:0px;"><img src="http://static.jquery.com/api/images/badge.jpg" style="border:0px;width:100%;"/></a>
      </div>
      <div class="sideBarFeature">
        <strong>jQuery API Book</strong>
        <a href="http://www.packtpub.com/" style="border-width:0;"><img src="http://static.jquery.com/books/jquery-ref-guide.png" style="border-width:0;"/></a>
      </div>
-->
    </div>

    <h2 class="jq-clearfix roundTop section-title">Browse the jQuery API</h2>
    <div class="roundBottom jq-box">
    <ul class="xoxo">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : // begin primary sidebar widgets ?>

      <li id="categories">
        <ul>
        <li><a href="/">All</a></li>
<?php
  $exclusion = ks_can_show_plugins() ? '' : '&exclude=85';

  wp_list_categories('depth=2&title_li=&use_desc_for_title=0&show_count=0&hierarchical=1=' . $exclusion);
?>
        </ul>
      </li>

<?php endif; // end primary sidebar widgets  ?>
  </ul>
  </div>
  </div><!-- #jq-interiorNavigation -->
