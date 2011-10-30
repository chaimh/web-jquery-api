<?php
/*
Template Name: Api
*/
header("Content-type: text/xml");
header("Cache-Control: no-cache");
echo '<?xml version="1.0" ?>';
?>
<api>
  <categories>
    <category name="Ajax">
      <category name="Global Ajax Event Handlers"/>
      <category name="Helper Functions"/>
      <category name="Low-Level Interface"/>
      <category name="Shorthand Methods"/>
    </category>
    <category name="Attributes"/>
    <category name="Core"/>
    <category name="CSS"/>
    <category name="Data"/>
    <category name="Deferred Object"/>
    <category name="Dimensions"/>
    <category name="Effects">
      <category name="Basics"/>
      <category name="Custom"/>
      <category name="Fading"/>
      <category name="Sliding"/>
    </category>
    <category name="Events">
      <category name="Browser Events"/>
      <category name="Document Loading"/>
      <category name="Event Handler Attachment"/>
      <category name="Event Object"/>
      <category name="Form Events"/>
      <category name="Keyboard Events"/>
      <category name="Mouse Events"/>
    </category>
    <category name="Forms"/>
    <category name="Internals" />
    <category name="Manipulation">
      <category name="Class Attribute"/>
      <category name="Copying"/>
      <category name="DOM Insertion"/>
      <category name="DOM Insertion, Around"/>
      <category name="DOM Insertion, Inside"/>
      <category name="DOM Insertion, Outside"/>
      <category name="DOM Removal"/>
      <category name="DOM Replacement"/>
      <category name="General Attributes"/>
      <category name="Style Properties"/>
    </category>
    <category name="Miscellaneous">
      <category name="Collection Manipulation"/>
      <category name="Data Storage"/>
      <category name="DOM Element Methods"/>
      <category name="Setup Methods"/>
    </category>
    <category name="Offset"/>
    <category name="Plugin Authoring"/>
    <category name="Properties">
      <category name="Properties of jQuery Object Instances "/>
      <category name="Properties of the Global jQuery Object"/>
    </category>
    <category name="Selectors">
      <category name="Attribute"/>
      <category name="Basic"/>
      <category name="Basic Filter"/>
      <category name="Child Filter"/>
      <category name="Content Filter"/>
      <category name="Form"/>
      <category name="Hierarchy"/>
      <category name="jQuery Extensions"/>
      <category name="Visibility Filter"/>
    </category>
    <category name="Traversing">
      <category name="Filtering"/>
      <category name="Miscellaneous Traversing"/>
      <category name="Tree Traversal"/>
    </category>
    <category name="Utilities"/>
    <category name="Version">
      <category name="Version 1.0"/>
      <category name="Version 1.0.4"/>
      <category name="Version 1.1"/>
      <category name="Version 1.1.2"/>
      <category name="Version 1.1.3"/>
      <category name="Version 1.1.4"/>
      <category name="Version 1.2"/>
      <category name="Version 1.2.3"/>
      <category name="Version 1.2.6"/>
      <category name="Version 1.3"/>
      <category name="Version 1.4"/>
      <category name="Version 1.4.1"/>
      <category name="Version 1.4.2"/>
      <category name="Version 1.4.3"/>
      <category name="Version 1.4.4"/>
      <category name="Version 1.5"/>
      <category name="Version 1.5.1"/>
      <category name="Version 1.6"/>
      <category name="Version 1.7"/>
    </category>
  </categories>
  <?php
  $lastposts = get_posts('numberposts=999');
  $corelist = array();
  $pluginlist = array();

  foreach($lastposts as $post) :
    setup_postdata($post);

    $theContent = get_the_content();

    $cats = '';
    foreach((get_the_category()) as $category) :
      $cats .= '<category name="' . $category->cat_name . '"/>' . "\n";
    endforeach;

    $notes_output = '';
    if ( function_exists('get_reus') ):
      $reus_vars = 'title=' . $post->post_title;
      $notes = array(
        'additional' => get_post_meta($post->ID, 'Note'),
        'banner' => get_post_meta($post->ID, 'Banner')
      );
      $notes_meta = get_post_meta($post->ID, 'Notemeta');

      if ( !empty($notes_meta) ) {
        foreach ($notes_meta as $meta) {
          $meta_parts = explode('=', $meta);
          $meta_key = array_shift($meta_parts);
          $meta_val = implode('=', $meta_parts);
          $meta = $meta_key . '=' . str_replace('=', '%3D', $meta_val);

          $reus_vars .= '&' . $meta;
        }
      }

      foreach ($notes as $key => $values):
        if ( !empty($values) ) {
          foreach ($values as $val):
            $note = get_reus($val, $reus_vars);
            $note = str_replace('%3D', '=', $note);
            $notes_output .= '<note type="' . $key . '">' . $note . '</note>';
          endforeach;
        }
      endforeach;
    endif;

    $cats_notes = $cats . $notes_output;

    $tentry = preg_replace("!</?entries>!", "", preg_replace("!</entry>!", "$cats_notes</entry>", $theContent));

    if ( ks_in_plugins_category($post->ID) ):
      $pluginlist[] = $tentry;
    else:
      $corelist[] = $tentry;
    endif;

  endforeach;
  ?>
  <entries>
    <?php echo implode("\n", $corelist) ?>
  </entries>
  <?php if ( count($pluginlist) ): ?>
  <plugins>
    <?php echo implode("\n", $pluginlist) ?>
  </plugins>
  <?php endif; ?>
</api>
