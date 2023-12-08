<?php
function find_location_based_on_current_url_home($segments)
{
  // If the first segment is 'locations'
  if (isset($segments[0]) && $segments[0] === 'locations') {
    // The second segment should be the location
    $location_slug = isset($segments[1]) ? $segments[1] : null;

    if ($location_slug) {
      $args = array(
        'name' => $location_slug,
        'post_type' => 'locations',
        'post_status' => 'publish',
        'numberposts' => 1
      );
      $location_posts = get_posts($args);

      // If a post is found
      if ($location_posts) {
        $location = $location_posts[0];
        return $location;
      } else {
        // No location was found
        return null;
      }
    }
  }

  return null;
}
