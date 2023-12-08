<?php
// Get Portfolio custom field data
function acf_about_data_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'field' => '',
  ), $atts, 'acf_about_data');

  $field_value = get_field($atts['field']);

  if (!$field_value) {
    return 'No value found for field "' . esc_html($atts['field']) . '".';
  }

  return $output = $field_value;
}
add_shortcode('acf_about_data', 'acf_about_data_shortcode');


// Get Team Holds Meetings Via Content
function acf_meetings_data_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'field1' => '',
    'field2' => '',
  ), $atts, 'acf_meetings_data');

  $field_value1 = get_field($atts['field1']);	// field
  $field_value2 = get_field($atts['field2']);	// name

  if (!$field_value1) {
    return;
  }

  // If $field_value is not an array, convert it to an array
  if (!is_array($field_value1)) {
    $field_value = array($field_value1);
  }

  $output = '<p class="supporteby-title">' . $field_value2 . ' HOLDS MEETINGS VIA</p>';

  $videoImg = '/wp-content/uploads/2023/05/monitor.svg';
  $phoneImg = '/wp-content/uploads/2023/05/IPHONE.svg';
  $inpersonImg = '/wp-content/uploads/2023/06/location-pin.svg';

  $output .= '<ul class="bio-meeting-icons">';
  foreach ($field_value1 as $key => $value) {
    if ($value === 'video') {
      $output .= '<li><div class="meeting-icon-box"><img class="meeting-icon" src="' . $videoImg . '"/><h3 class="meeting-label">VIDEO CALL</h3></li>';
    }
    if ($value === 'phone') {
      $output .= '<li><div class="meeting-icon-box"><img class="meeting-icon" src="' . $phoneImg . '"/><h3 class="meeting-label">PHONE CALL</h3></li>';
    }
    if ($value === 'person') {
      $output .= '<li><div class="meeting-icon-box"><img class="meeting-icon" src="' . $inpersonImg . '"/><h3 class="meeting-label">IN PERSON</h3></li>';
    }
  }
  $output .= '</ul>';

  return $output;
}
add_shortcode('acf_meetings_data', 'acf_meetings_data_shortcode');


// Get Team Hobbies Content
function acf_hobbies_data_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'field1' => '',
    'field2' => '',
  ), $atts, 'acf_hobbies_data');

  $field_value1 = get_field($atts['field1']);	// field
  $field_value2 = get_field($atts['field2']);	// name

  if (!$field_value1) {
    return;
  }

  // If $field_value is not an array, convert it to an array
  if (!is_array($field_value1)) {
    $field_value = array($field_value1);
  }

  $output = '<h2>IN THEIR SPARE TIME, ' . $field_value2 . ' CAN BE FOUND</h2>';
  $output .= '<p>' . $field_value1 . '</p>';

  return $output;
}
add_shortcode('acf_hobbies_data', 'acf_hobbies_data_shortcode');


// Get Team Supported By Content
function acf_portfolio_data_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'field1' => '',
    'field2' => '',
  ), $atts, 'acf_portfolio_data');

  $field_value1 = get_field($atts['field1']);	// field
  $field_value2 = get_field($atts['field2']);	// name

  if (!$field_value1) {
    return;
  }

  // Convert array to comma-separated string
  if (is_array($field_value1)) {
    $field_value1 = implode(',', $field_value1);
  }

  $output = '<p class="supporteby-title">' . $field_value2 . ' IS SUPPORTED BY</p>';
  $output .= '<div class="supportedby-wrapper">';

  // Handle multiple titles
  $titles = explode(',', $field_value1);
  foreach ($titles as $title) {
    // Query for the portfolio post by title
    $args = array(
      'post_type' => 'avada_portfolio',
      'post_status' => 'publish',
      'posts_per_page' => 1,
      'name' => $title,
    );
    $portfolio_query = new WP_Query($args);

    if ($portfolio_query->have_posts()) {
      $portfolio_query->the_post();

      $name = get_the_title();

      // Get the portfolio categories
      $portfolio_categories = get_the_terms(get_the_ID(), 'portfolio_category');
      if (!empty($portfolio_categories)) {
        $data['category'] = esc_html($portfolio_categories[0]->name);
        $job = esc_html($portfolio_categories[0]->name);
      }

      $output .= '<div class="supportedby-box">';
      $output .= '<img class="supportedby-avatar" src="' . get_the_post_thumbnail_url() . '" alt="' . $name . '">';
      $output .= '<div class="supportedby-details">';
      $output .= '<h3 class="supportedby-name">' . $name . '</h3>';
      $output .= '<h3 class="supportedby-job">' . $job . '</h3>';
      $output .= '</div>';
      $output .= '</div>';

      wp_reset_postdata();
    } else {
      return;
    }
  }
  return $output;
}
add_shortcode('acf_portfolio_data', 'acf_portfolio_data_shortcode');
