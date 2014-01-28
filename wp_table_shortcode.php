<?php
/*
 * Plugin Name: WP Table Shortcode
 * Plugin URI: https://github.com/siruguri/wp_table_shortcode
 * Description: Adds a [table] shortcode that generates a spreadsheet (uses theme's CSS)
 * Version: 0.1
 * Author: Sameer Siruguri
 * Author URI: http://sameer.siruguri.net/blog/
 * License: GPL2
*/

function simple_table_wrap($atts, $content = null) {
  $max_cols = 0;

  $ret_val=<<<EODOC
<div class="simple_table">
<table>
<tbody>
<tr>
<td>@</td>
EODOC;

  $rows = explode("\n", $content);
  $row_letter='A';
  $row_string_list=array();

  foreach ($rows as $line) {

    // Ignore the plain <p> that Wordpress/TinyMCE adds to lines
    if(preg_match("#^\s*<(/)?p>\s*$#", $line)) {
      continue;
    }

    $cells = preg_split("/\s*\|\s*/", $line);

    // Start initializing the string that will generate this row
    $row_string = "";
    $row_string .= ("<tr><td>".$row_letter."</td>");

    // Keep track of how wide the table is
    if (count($cells) > $max_cols) {
      $max_cols = count($cells);
    }

    foreach ($cells as $value) {
      $value = preg_replace("#</?p>#", "", $value);

      // Because we are splitting on the pipe symbol, there might be leading whitespace

      $value = preg_replace("/^\s*/", "", $value);

      $row_string .= '<td id="cellvalue">'. $value . '</td>';
    }

    $row_letter ++;
    array_push ($row_string_list, array('length' => count($cells), 'string' => $row_string ));
  }

  // construct the actual html from the indiv row strings
  $count=1;

  for ($i=1; $i <= $max_cols; $i++) {
    $ret_val .= "<td>" . $i . "</td>";
  }
  $ret_val .= "</tr>";

  $final_row_string_list = array_map(function($x) use($max_cols) {
      for($i=0; $i<$max_cols-$x['length']; $i++) {
	$x["string"].="<td id='cellvalue'></td>";
      }
      $x['string'].="</tr>";
      return $x['string'];

    }, $row_string_list);

  $ret_val .= implode("", $final_row_string_list);

  $ret_val .= '</tbody></table></div>';
  return $ret_val; 
}

add_shortcode('simple_table', 'simple_table_wrap');
/* 

*/

?>
