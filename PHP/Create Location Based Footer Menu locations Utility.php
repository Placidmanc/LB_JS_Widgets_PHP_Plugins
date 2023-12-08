<?php
function update_location_menu()
{
  $location = 'location-specific-menu';
  $menu_name = 'Locations Specific Menu';

  $menu_id = wp_create_nav_menu($menu_name);

  if (is_wp_error($menu_id)) {
    $menu_obj = get_term_by('name', $menu_name, 'nav_menu');
    $menu_id = $menu_obj->term_id;
  }

  // Get existing menu items
  $existing_menu_items = wp_get_nav_menu_items($menu_id);

  // Create an array to store existing post IDs in the menu
  $existing_post_ids = [];

  // Loop through existing menu items and populate our array
  foreach ($existing_menu_items as $menu_item) {
    $existing_post_ids[] = $menu_item->object_id;
  }

  // Get all posts from 'locations' CPT
  $locations = get_posts([
    'post_type' => 'locations',
    'numberposts' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
  ]);

  foreach ($locations as $location) {
    // Check if this post ID already exists in the menu
    if (!in_array($location->ID, $existing_post_ids)) {
      wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => $location->post_title,
        'menu-item-object' => 'page',
        'menu-item-object-id' => $location->ID,
        'menu-item-type' => 'post_type',
        'menu-item-status' => 'publish'
      ]);
    }
  }

  if (is_int($menu_id)) {
    $locations = get_theme_mod('nav_menu_locations'); // Get all menu locations
    $locations[$location] = $menu_id;  // Set the location in the array
    set_theme_mod('nav_menu_locations', $locations); // Update theme mod
  }
}
add_action('wp_update_nav_menu', 'update_location_menu');
