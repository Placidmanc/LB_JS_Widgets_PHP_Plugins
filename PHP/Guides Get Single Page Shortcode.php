<?php
function split_section_title($title, $section)
{
  $words = explode(' ', $title);
  $split_index = ceil(count($words) / 2);
  $first_group = array_slice($words, 0, $split_index);
  $second_group = array_slice($words, $split_index);

  if ($section === 'sub') {
    return '<h2 class="guide-section-title">' . implode(' ', $first_group) . '<br><span style="font-weight: 900;">' .
      implode(' ', $second_group) . '</span></h2>';
  } else if ($section === 'main') {
    return '<h3 class="guide-main-title">' . implode(' ', $first_group) . '<br><span style="font-weight: 500;">' . implode('
    ', $second_group) . '</span></h3>';
  }
  return null;
}

function acf_guide_shortcode()
{
  $post_id = get_the_ID();

  $output = '';
  $output .= '<h2 class="guide-links-title">GUIDE <span style="font-weight:900;">LINKS</span></h2>';

  // Create guide links
  $output .= '<section class="guide-links">';

  if (have_rows('section', $post_id)) {
    // Loop through the sections
    while (have_rows('section', $post_id)) {
      the_row();

      // Append the section title
      $section_title = get_sub_field('section_title');
      if ($section_title) {
        $output .= '<a href="#' . $section_title . '">
    <div class="guide-link-box">';
        $output .= '<p>' . $section_title . '</p>';
        $output .= '</div>
  </a>';
      }
    }
  }
  $output .= '</div>';
  // End guide links

  // Check if we have any repeater sections
  if (have_rows('section', $post_id)) {
    // Loop through the sections
    while (have_rows('section', $post_id)) {
      the_row();

      // Get the section title to use as an anchor
      $section_title = get_sub_field('section_title');

      // Start the section
      if ($section_title) {
        $output .= '<section id="' . $section_title . '" class="guides-wrapper">';
      } else {
        $output .= '<section class="guides-wrapper">';
      }

      // Start the left column
      $output .= '<div class="guides-left">';

      // create a sticky box
      $output .= '<div class="guide-section-sticky-box">';

      // Append the section title
      if ($section_title) {
        $output .= split_section_title($section_title, 'sub');
      }

      // Append the section image
      $section_image = get_sub_field('section_image');
      if ($section_image) {
        $output .= '<img src="' . $section_image . '" width="211" />';
      }

      $output .= '<h3 class="guide-aag-title">AT A GLANCE</h3>';

      // Check if we have any content in the section
      if (have_rows('section_content')) {
        // Loop through the content
        while (have_rows('section_content')) {
          the_row();

          // Start the icon list
          $output .= '<div class="guide-aag-icon-list">';

          // Append the at a glance section icon
          $at_a_glance_section_icon = get_sub_field('at_a_glance_section_icon');
          if ($at_a_glance_section_icon) {
            $output .= '<img src="' . $at_a_glance_section_icon . '" width="20" />';
          }

          // Append the at a glance section short title
          $at_a_glance_section_short_title = get_sub_field('at_a_glance_section_short_title');
          if ($at_a_glance_section_short_title) {
            $output .= '<p class="guide-aag-short-title">' . $at_a_glance_section_short_title . '</p>';
          }

          $output .= '</div>';
        }
      }

      // End the sticky box
      $output .= '</div>';

      // End the left column
      $output .= '</div>';

      // Start the right column
      $output .= '<div class="guides-right guide-content-wrapper">';

      if (have_rows('section_content')) {
        // Loop through the content
        while (have_rows('section_content')) {
          the_row();

          // Append the sub section title
          $sub_section_title = get_sub_field('sub_section_title');
          if ($sub_section_title) {
            $output .= split_section_title($sub_section_title, 'main');
          }

          // Append the sub section copy
          $sub_section_copy = get_sub_field('sub_section_copy');
          if ($sub_section_copy) {
            $output .= '<p>' . $sub_section_copy . '</p>';
          }
        }
      }

      // End the right column
      $output .= '</div>';

      // End the section
      $output .= '</section>';
    }
  }

  return $output; // Return the final output string
}
add_shortcode('acf_guide_fields', 'acf_guide_shortcode');
