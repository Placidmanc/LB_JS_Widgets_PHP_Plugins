<?php
function create_buttons($post_id)
{
  $row_counter = 1;

  $output = '<div class="split-lh">';

  if (have_rows('content', $post_id)) {
    while (have_rows('content', $post_id)) {
      the_row();

      $button_label_top_line = get_sub_field('button_label_top_line');
      $button_label_bottom_line = get_sub_field('button_label_bottom_line');

      if ($button_label_top_line && $button_label_bottom_line) {
        // Set the first button as active so the UI is correct before waiting for load
        if ($row_counter === 1) {
          $output .= '<div class="hdiw-split-btn splitBtnActive" id="splitbtn' . $row_counter . '">';
        } else {
          $output .= '<div class="hdiw-split-btn" id="splitbtn' . $row_counter . '">';
        }
        $output .= '<h2>' . $row_counter . '</h2>';
        $output .= '<p>' . $button_label_top_line . '<br>' . $button_label_bottom_line . '</p>';
        $output .= '</div>';
      }

      $row_counter++;
    }
  }
  $output .= '</div>';	// End split-lh

  return $output;
}

function create_content($post_id)
{
  $output = '<div class="split-rh">';

  $row_counter = 1;

  if (have_rows('content', $post_id)) {
    while (have_rows('content', $post_id)) {
      the_row();

      if ($row_counter === 1) {
        $output .= '<div id="split' . $row_counter . 'info">';
      } else {
        $output .= '<div id="split' . $row_counter . 'info" style="display:none;">';
      }

      $content_header_bold = get_sub_field('content_header_bold');
      $content_header_thin = get_sub_field('content_header_thin');
      $toggle_header_line_break = get_sub_field('toggle_header_line_break');
      $content_body = get_sub_field('content_body');

      $output .= '<div class="splitinfo-box">';

      if ($toggle_header_line_break) {
        $output .= '<h3>' . $content_header_bold . ' <br><span style="font-weight: 300;">' . $content_header_thin . '</span></h3>';
      } else {
        $output .= '<h3>' . $content_header_bold . ' <span style="font-weight: 300;">' . $content_header_thin . '</span></h3>';
      }

      $output .= '<p>' . $content_body . '</p>';

      // Check if we have any list items
      if (have_rows('whos_involved_list')) {

        $whos_involved_list_title = get_sub_field('whos_involved_list_title');
        if ($whos_involved_list_title) {
          $output .= '<h4>' . $whos_involved_list_title . '</h4>';
        }

        $output .= '<ul class="wi-coloured-li">';

        // Loop through the content
        while (have_rows('whos_involved_list')) {
          the_row();

          $list_item = get_sub_field('whos_involved_list_item');
          $bullet_colour = get_sub_field('whos_involved_list_item_bullet_colour');

          // Set the style property '--li-bg-colour' so the CSS can get the colour 
          $output .= '<li class="whos_involved_list_item" style="--li-bg-colour: ' . $bullet_colour . '">' . $list_item . '</li>';
        }

        $output .= '</ul>';
      }

      // If there's a button to show...
      $show_button = get_sub_field('show_button');

      if ($show_button) {
        $button_label = get_sub_field('button_label');
        $button_link = get_sub_field('button_link');
        $output .= '<div class="read-more-box"><a href="' . $button_link . '" class="read-more-btn">' . $button_label . '</a></div>';
      }

      $output .= '</div>';
      $output .= '</div>';

      $row_counter++;
    }
  }
  $output .= '</div>'; // End of split-rh
  return $output;
}

function howdoesxwork_shortcode($atts)
{
  $attributes = shortcode_atts(
    array(
      'page' => ''
    ),
    $atts
  );

  if (empty($attributes['page'])) {
    return '';
  }

  $post = get_page_by_path($attributes['page'], OBJECT, 'how_does_x_work');
  if (!$post) {
    $post = get_post($attributes['page']);
  }

  if (!$post || $post->post_type !== 'how_does_x_work') {
    return '';
  }

  // create the content
  $output = '<div class="howdoesxwork-wrapper">';			// Start the content wrapper

  $output .= '<div class="split-wrapper">';			// Start the inner content wrapper
  $output .= create_buttons($post->ID);				// Create the buttons
  $output .= create_content($post->ID);				// Create the content blocks
  $output .= '</div>';								// End split-wrapper

  $output .= '</div>';								// End content wrapper

  return $output;
}
add_shortcode('howdoesxwork', 'howdoesxwork_shortcode');
