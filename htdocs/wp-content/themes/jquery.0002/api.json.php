<?php
/*
Template Name: JSON Api
*/
?>
<?php
$apivars = array();

if (isset($_GET)):
  foreach ($_GET as $key => $value) {
    if ( !empty($value) ) {
      $apivars[$key] = $value;
    }
  }
endif;

$posts = apiquery($apivars);

// Process the posts returned by apiquery()
$corelist = array();
$pluginlist = array();
foreach($posts as $post) :

  setup_postdata($post);

  $theContent = get_the_content();

  $cats = array();
  foreach((get_the_category()) as $category) :
    $catparents = get_category_parents($category->term_id, false, ' &gt; ');
    $cats[] = substr($catparents, 0, -6);
  endforeach;
  $catslugs = implode(', ', $cats);

  if ( @$apivars['version'] && strpos($catslugs, @$apivars['version']) === false ):
    continue;
  endif;
  $cats = '<categories>' . $catslugs . '</categories>';
  $theContent = preg_replace("!</?entries>!", "", $theContent);
  preg_replace("!</entry>!", "$cats</entry>", $theContent);

  $entrymeta = '$1><slug>$2' . '</slug><title>' . $post->post_title . '</title>';
  $entrymeta .= '<url>' . get_permalink() . '</url>';
  $theContent = preg_replace('@(<entry[^>]*?)name="([^"]+)"[^>]*>@', $entrymeta, $theContent);

  if ( ks_in_plugins_category() ):
    $pluginlist[] = $theContent;
  else:
    $corelist[] = $theContent;
  endif;

endforeach;

$entrystring = '';
if (!isset($apivars['core']) || @ $apivars['core'] != 'false') {
  $corelist = array_unique($corelist);
  $entrystring .= implode('', $corelist);
}
if (isset($apivars['plugins']) ) {
  $pluginlist = array_unique($pluginlist);
  $entrystring .= $apivars['plugins'] == 'true' ? implode('', $pluginlist) : '';
}


// Convert XML from entry results into an associative array
$api_string = <<<XML
<api>
  <entries>
  $entrystring;
   </entries>
</api>
XML;

$xml = simplexml_load_string($api_string);
$api = array();
$descpattern = '@</?desc>@';

foreach ($xml->entries->entry as $entrytemp) {
  $entry = simplexml_load_string($entrytemp->asXML());
  $attrs = $entry->attributes();
  $title = (string)$entry->title[0];
  $slug = (string)$entry->slug[0];
  $url = (string)$entry->url[0];

  $sigs = array();
  foreach ($entry->signature as $sig) {

    $args = array();
    foreach ($sig->argument as $arg) {
      $argparams = $arg->attributes();
      $thisarg = array(
        'name' => (string)$argparams['name'][0],
        'type' => (string)$argparams['type'][0],
        'optional' => (string)$argparams['optional'] ? 'optional' : '',
        'desc' => preg_replace( $descpattern, '', $arg->desc->asXml() ),
      );
      $argoptions = build_options($arg->option);
      if ( !empty($argoptions) ) {
        $thisarg['options'] = $argoptions;
      }
      $args[] = $thisarg;
    }

    $currentSig = array(
      'added' => (string)$sig->added,
    );

    if (count($args)) {
      $currentSig['params'] = $args;

      // special case. Pretty sure this is just used in $.ajax()
      $opts = build_options($sig->option);
      if ( count($opts) ) {
        $currentSig['options'] = $opts;
      }
    }
    $props = build_options($sig->property);
    if ( count($props) ) {
      $currentSig['properties'] = $props;
    }

    $sigs[] = $currentSig;
  }

  $newentry = array(
    'url' => $url,
    'name' => $slug,
    'title' => $title,
    'type' => (string)$attrs['type'],
    'signatures' => $sigs,
    'desc' => inner_html($entry, 'desc'),
    'longdesc' => inner_html($entry, 'longdesc'),
    'download' => inner_html($entry, 'download'),

  );

  if ( (string)$attrs['return'] ) {
    $newentry['return'] = (string)$attrs['return'];
  }
  $api[] = $newentry;
}

// Convert associative array of results to JSON
// add "padding" for JSONP if callback specified
// and echo it
  $json_api = json_encode($api);
  $callback = isset($_GET['callback']) ? $_GET['callback'] : false;

  if ($callback):
    header ("Content-Type: text/javascript; charset=UTF-8");
    echo $callback . '(' . $json_api . ')';

  else:
    header ("Content-Type: application/json; charset=UTF-8");
    echo $json_api;

  endif;

/*
* Get inner html contents of a node
*/
function inner_html($obj, $nodename) {
  $innerhtml = $obj->{$nodename}->asXML();
  if (!$innerhtml) {
    return '';
  }

  $nodepattern = '@</?' . $nodename . '>@';
  $innerhtml = preg_replace($nodepattern, '', $innerhtml);

  return $innerhtml;
}

function build_options($options) {
  global $descpattern;
  $opts = array();
  foreach ($options as $opt) {
    $optparams = $opt->attributes();
    $op = array();
    foreach ($optparams as $key => $value) {
      $op[$key] = (string)$optparams[$key][0];
    }
    $op['desc'] = preg_replace( $descpattern, '', $opt->desc->asXml() );
    $opts[] = $op;
  }
  return $opts;
}
/*
* Build a list of entries using the query
*/
function apiquery($vars) {
  global $wpdb;

  $tables = array('wp_posts as p');
  $wheres = array("p.post_status='publish' AND p.post_type = 'post'");
  $wherecat = array();
  $usedcat = false;

  $matchtype = @$vars['match'];
  switch ($matchtype) {
    case 'exact':
      $typereplace = array("= '", "'" );
      break;

    case 'start':
      $typereplace = array(" LIKE '", "%'" );
      break;

    case 'end':
      $typereplace = array(" LIKE '%", "'" );
      break;

    default:
      $typereplace = array(" LIKE '%", "%'" );
      break;
  }

  foreach ($vars as $key => $param):

    if ( $key == 'title' ):
      $term = trim($param);
      $termpatterns = array(
        '/^\$/',
        '/[\s\+]+/',
      );
      $termsubs = array(
        'jQuery',
        '-',
      );

      $term = preg_replace($termpatterns, $termsubs, $term);

      $title_search = "((p.post_title PRE$term" . "POST) OR (p.post_name PRE$term" . "POST))";
      $title_search = preg_replace( array('/PRE/', '/POST/'), $typereplace, $title_search);
      $wheres[] = $title_search;

    endif;

    $categories = array();

    if ($key == 'category'):
      $getcats = get_categories();
      $catpattern = '/' . strtolower($param) . '/';

      foreach ($getcats as $getcat) {
        $getcatname = strtolower($getcat->cat_name);
        $getcatid = $getcat->term_id;

        if (preg_match($catpattern, $getcatname)):
          $categories[] = $getcatid;
        endif;
      }

      $incats =  implode(',', $categories);
      $wherecat[] = "tt.term_taxonomy_id IN ($incats) AND tr.term_taxonomy_id IN ($incats) OR tt.parent IN ($incats)";
      $usedcat = true;

    elseif ($key == 'version' && !array_key_exists('category', $vars)):
      $usedcat = true;
      $versions = array();
      $getversions = get_categories('child_of=69');

      switch ($matchtype) {
        case 'exact':
          $versionpattern = '/^' . $param . '$/';
          break;

        case 'end':
          $versionpattern = '/' . $param . '$/';
          break;
        default:
          $versionpattern = '/^' . $param . '/';
          break;
      }

      foreach ($getversions as $getversion) {
        if (preg_match($versionpattern, $getversion->slug)):
          $versions[] = $getversion->term_id;
        endif;
      }

      if (count($versions) == 1) {
        $version = implode('', $versions);
        $wherecat[] = "tt.term_taxonomy_id = $version AND tr.term_taxonomy_id = $version";
      } elseif (count($versions) > 1) {
        $inversions =  implode(',', $versions);
        $wherecat[] = "tt.term_taxonomy_id IN ($inversions) AND tr.term_taxonomy_id IN ($inversions)";
      } else {
        $usedcat = false;
      }

    endif;

  endforeach;


  if ($usedcat):
    $tables[] = "wp_term_taxonomy as tt";
    $tables[] = "wp_term_relationships as tr";

    $wherecat[] = "tr.term_taxonomy_id=tt.term_taxonomy_id";
    $wheres[] = "(" . implode(' AND ', $wherecat) . ") AND tr.object_id=p.ID";
  endif;

  $tables = implode(', ', $tables);
  $wherestatement = implode(' AND ', $wheres);
  $query = "SELECT * FROM $tables WHERE $wherestatement ORDER BY p.post_name;";

  $postquery = $wpdb->get_results($query);

  return $postquery;
}
?>
