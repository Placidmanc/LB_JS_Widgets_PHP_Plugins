<?php

function create_who_header($post_id)
{
  global $wp;

  // Get the path to find the location as the page isn't a CPT
  $path = $wp->request;
  $segments = explode('/', trim($path, '/'));

  $main_title_thin = get_field('main_title_thin', $post_id);
  if (isset($segments[0]) && $segments[0] === 'locations') {
    $main_title_bold = 'IN A ' . strtoupper($segments[1]) . ' AUCTION? ';
  } else {
    $main_title_bold = get_field('main_title_bold', $post_id);
  }

  return '<h2 class="section-hdr-thin-bold-left">' . $main_title_thin . '<br><span style="font-weight:900;">' . $main_title_bold . '</span></h2>';
}

function create_who_content($post_id)
{
  $who_image = get_field('who_image', $post_id);
  $who_strapline = get_field('who_strapline', $post_id);
  $who_copy = get_field('who_copy', $post_id);
  $button_label = get_field('button_label', $post_id);
  $button_url = get_field('button_url', $post_id);

  $output = '<div class="whos-involved-wrapper">';
  $output .= '<div class="who-left">';
  $output .= '<img src="' . $who_image . '">';
  $output .= '<h3>' . $who_strapline . '</h3>';
  $output .= create_who_header($post_id);
  $output .= '<p>' . $who_copy . '</p>';
  $output .= '<a href="' . $button_url . '">' . $button_label . '</a>';
  $output .= '</div>';

  $row_counter = 1;
  $output .= '<div class="who-right">';

  if (have_rows('who_repeater', $post_id)) {
    while (have_rows('who_repeater', $post_id)) {
      the_row();

      $who_label = get_sub_field('who_label');
      $who_description = get_sub_field('who_description');

      $output .= '<div class="who-btn" id="who-btn-' . $row_counter . '">';
      $output .= '<div class="who-btn-box">';
      $output .= '<img src="/wp-content/uploads/2023/06/target-purple-blue-pink.svg" />';
      $output .= '<p>' . $who_label . '</p>';
      $output .= '</div>';

      if ($row_counter === 1) {
        $output .= '<div class="who-content" id="who-content-' . $row_counter . '" style="display:block;">';
      } else {
        $output .= '<div class="who-content" id="who-content-' . $row_counter . '" style="display:none;">';
      }

      $output .= '<p>' . $who_description . '</p>';
      $output .= '</div>';
      $output .= '</div>';

      $row_counter++;
    }
  }

  $output .= '</div>';  // End of who-right
  $output .= '</div>'; 	// End of wrapper

  return $output;
}

function whos_involved_shortcode($atts)
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

  $post = get_page_by_path($attributes['page'], OBJECT, 'whos_involved');
  if (!$post) {
    $post = get_post($attributes['page']);
  }

  if (!$post || $post->post_type !== 'whos_involved') {
    return '';
  }

  // Create the content
  return create_who_content($post->ID);
}
add_shortcode('whos_involved', 'whos_involved_shortcode');
