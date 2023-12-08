<?php
function review_solicitors_widget_func($atts)
{
  // Extract shortcode attributes
  $atts = shortcode_atts(array(
    'post_id' => get_the_ID(),
  ), $atts, 'review_solicitors_widget');

  // Get the value of the ACF field
  $widget_code = get_field('review_solicitors_widget_code', $atts['post_id']);

  if (!$widget_code) {
    return;
  }
  return $widget_code;
}
add_shortcode('review_solicitors_widget', 'review_solicitors_widget_func');
