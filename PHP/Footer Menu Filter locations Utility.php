<?php
function update_menu_links_based_on_location($items, $args)
{
  $original_pages = array(
    'services' => array(
      '_id' => 11,
      'property-law' => array(
        '_id' => 15001,
        'buying-property' => 15444,
        'selling-a-property' => 15771,
        'new-build' => 15789,
        'modern-auction' => 20219,
        'traditional-auction' => 18467,
        'transfer-of-equity' => 20032,
        'remortgage' => 20170,
      ),
      'wills-probate' => array(
        '_id' => 13141,
        'local-will-solicitors' => 14415,
        'make-an-lpa' => 14716,
        'probate-solicitors' => 14208,
      ),
      'family-law' => array(
        '_id' => 12449,
        'divorce-lawyers' => 12935,
        'child-arrangements' => 13378,
        'financial-arrangements' => 13380,
      ),
    ),
  );

  $current_location_slug = get_location_slug();

  if (!$current_location_slug) {
    return $items;
  }

  $location_path = '/locations/' . $current_location_slug;

  foreach ($items as $item) {
    // Check if the item is one of the original pages
    $item_id = $item->object_id;
    $should_update = false;

    foreach ($original_pages['services'] as $category => $category_data) {
      if ($category !== '_id' && in_array($item_id, $category_data)) {
        $should_update = true;
        break;
      }
    }

    if (!$should_update) {
      continue;
    }

    // Check if '/locations/' already exists in the URL
    if (strpos($item->url, '/locations/') !== false) {
      continue; // Skip this iteration as '/locations/' is already present.
    }

    // Remove the home URL from the current item URL to get only the path
    $item_path_only = str_replace(home_url(), '', $item->url);

    // Update the URL to include the location
    $item->url = home_url() . $location_path . $item_path_only;
  }

  return $items;
}
add_filter('wp_get_nav_menu_items', 'update_menu_links_based_on_location', 20, 2);



function get_location_slug()
{
  global $wp;

  $current_url_path = home_url($wp->request);

  // Remove the home URL to get the path only
  $path_only = str_replace(home_url(), '', $current_url_path);

  // Split the URL into segments
  $segments = explode('/', trim($path_only, '/'));

  // Find the index of 'locations'
  $index = array_search('locations', $segments);

  // If 'locations' is found and the next index exists
  if ($index !== false && isset($segments[$index + 1])) {
    return $segments[$index + 1]; // Return the segment after 'locations'
  }

  return null; // If no location slug is found
}
