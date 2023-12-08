<?php
add_action('wp_head', function () {
  global $wp, $post;

  $path = $wp->request;
  $segments = explode('/', trim($path, '/'));

  // Find the last segment that could be a slug
  $possible_slugs = array_slice($segments, -1);

  // Access its properties like $location->post_title, $location->ID, etc.
  $location = find_location_based_on_current_url($segments);
  $location_id = $location->ID;

  // Get the Local Business Info for the location (schema)
  if (have_rows('global_meta', $location_id)) {
    while (have_rows('global_meta', $location_id)) {
      the_row();
      $business_schema = get_sub_field('local_business_info');
    }
  }

  if (count($segments) > 2) {
    // All pages other than location/Home
    $current_slug = $post->post_name; // Get the current page slug
    $is_duplicated = get_field('is_duplicated_page_' . $current_slug, $location_id, true);
    $original_page_id = get_field('original_page_id_' . $current_slug, $location_id, true);

    if (is_singular('page') && $is_duplicated) {
      set_meta($original_page_id, $business_schema);
    }
  } else {
    // location/Home page
    $is_duplicated = get_field('is_duplicated_page_home', $location_id, true);
    $original_page_id = get_field('original_page_id_home', $location_id, true);

    if (is_singular('page') && $is_duplicated) {
      set_meta($original_page_id, $business_schema, true);
    }

  }
});


function set_meta($original_page_id, $business_schema, $is_home = false)
{
  if ($is_home) {
    echo '<link rel="canonical" href="https://lyonsbowe.co.uk/" />';
  } else {
    echo '<link rel="canonical" href="' . get_permalink($original_page_id) . '" />';
  }
  echo '<meta name="robots" content="noindex, follow" />';
  echo '<script type="application/ld+json">' . json_encode($business_schema) . '</script>';
}


function find_location_based_on_current_url($segments)
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

add_action('wp_footer', function () {
  // Remove tags if the URL is location specific
  echo '<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function() {
			if (window.location.href.includes("locations")) {
				var head = document.getElementsByTagName("head")[0];
				var linkTags = head.querySelectorAll("link[rel=\'canonical\']");
				var metaTags = head.querySelectorAll("meta[name=\'robots\']");

				linkTags.forEach(function(tag) {
					if (tag.href.includes("location")) {
						// Remove this tag
						head.removeChild(tag);
					}
				});

				metaTags.forEach(function(tag) {
					if (tag.content.includes("max")) {
						head.removeChild(tag);
					}
				});
			}
		});
	</script>';
});
