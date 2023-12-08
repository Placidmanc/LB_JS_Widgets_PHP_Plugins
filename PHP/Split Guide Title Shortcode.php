<?php
function split_guide_title_shortcode()
{
  $title = get_the_title();
  $words = explode(' ', $title);

  $split_index = ceil(count($words) / 2);
  $first_group = array_slice($words, 0, $split_index);
  $second_group = array_slice($words, $split_index);
  $formatted_title = '<h2 class="guide-post-title">' . implode(' ', $first_group) . '<br><span style="font-weight: 900;">' . implode(' ', $second_group) . '</span></h2>';

  return $formatted_title;
}
add_shortcode('split_guide_title', 'split_guide_title_shortcode');
