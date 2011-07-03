<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ) ?></title>
  <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
<?php if (is_single()): ?>
<?php wp_head() // For plugins ?>
<?php endif; ?>
  <link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
  <link rel="stylesheet" href="http://static.jquery.com/files/rocker/css/reset.css" type="text/css" />
  <link rel="stylesheet" type="text/css" href="http://static.jquery.com/api/style.css" />
  <link rel="stylesheet" href="http://static.jquery.com/api/prettify.css">
  <script src="http://code.jquery.com/jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="http://static.jquery.com/files/rocker/scripts/custom.js"></script>
  <script type="text/javascript" src="http://static.jquery.com/api/jquery.livesearch.js"></script>
  <script type="text/javascript" src="http://static.jquery.com/api/navi.js"></script>
  <link rel="shortcut icon" href="http://static.jquery.com/favicon.ico" type="image/x-icon"/>
</head>

<body id="jq-interior" class="api-jquery-com <?php echo is_single() ? "single" : ""; ?>">


  <div id="jq-siteContain">
      <div id="jq-header">
        <a id="jq-siteLogo" href="http://jquery.com/" title="jQuery Home"><img src="http://static.jquery.com/files/rocker/images/logo_jquery_215x53.gif" width="215" height="53" alt="jQuery: Write Less, Do More." /></a>
        <div id="jq-primaryNavigation">
          <ul>
<li class="jq-jquery jq-current"><a href="http://jquery.com/" title="jQuery Home">jQuery</a></li>
<li class="jq-plugins"><a href="http://plugins.jquery.com/" title="jQuery Plugins">Plugins</a></li>
<li class="jq-ui"><a href="http://jqueryui.com/" title="jQuery UI">UI</a></li>
<li class="jq-meetup"><a href="http://meetups.jquery.com/" title="jQuery Meetups">Meetups</a></li>
<li class="jq-forum"><a href="http://forum.jquery.com/" title="jQuery Forum">Forum</a></li>
<li class="jq-blog"><a href="http://blog.jquery.com/" title="jQuery Blog">Blog</a></li>
<li class="jq-about"><a href="http://jquery.org/about" title="About jQuery">About</a></li>
<li class="jq-donate"><a href="http://jquery.org/donate" title="Donate to jQuery">Donate</a></li>
          </ul>
        </div><!-- /#primaryNavigation -->

        <div id="jq-secondaryNavigation">
          <ul>
            <li class="jq-download jq-first"><a href="http://docs.jquery.com/Downloading_jQuery">Download</a></li>
            <li class="jq-documentation jq-current"><a href="http://docs.jquery.com/">Documentation</a></li>
            <li class="jq-tutorials"><a href="http://docs.jquery.com/Tutorials">Tutorials</a></li>
            <li class="jq-bugTracker"><a href="http://dev.jquery.com/">Bug Tracker</a></li>
            <li class="jq-discussion jq-last"><a href="http://docs.jquery.com/Discussion">Discussion</a></li>
          </ul>
        </div><!-- /#secondaryNavigation -->



        <form id="jq-primarySearchForm" action="<?php bloginfo('home') ?>" method="get" autocomplete="off">
          <div>
            <input type="hidden" value="1" name="ns0"/>
            <label for="jq-primarySearch">Search <span class="jq-jquery">jQuery</span></label>
            <input type="text" value="" accesskey="f" title="Search jQuery" name="s" id="jq-primarySearch" class=""/>
            <button type="submit" name="go" id="jq-searchGoButton"><span>Go</span></button>
          </div>
        </form>

<!--
        <h1 id="blog-title"><span><a href="<?php bloginfo('home') ?>/" title="<?php echo wp_specialchars( get_bloginfo('name'), 1 ) ?>" rel="home"><?php bloginfo('name') ?></a></span></h1>
        <div id="blog-description"><?php bloginfo('description') ?></div>
-->
        <h1><?php bloginfo('name') ?></h1>


      </div><!-- /#header -->

      <div id="jq-content" class="jq-clearfix">




  <div id="access">
    <div class="skip-link"><a href="#content" title="<?php _e( 'Skip to content', 'sandbox' ) ?>"><?php _e( 'Skip to content', 'sandbox' ) ?></a></div>
    <?php sandbox_globalnav() ?>
  </div>
<!-- #access -->
