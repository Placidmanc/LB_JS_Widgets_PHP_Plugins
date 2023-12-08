<?php
function delete_associated_pages_on_location_delete($post_id)
{
  // If it's not a 'locations' post type, return
  if (get_post_type($post_id) != 'locations') {
    return;
  }

  // Get the ACF field with the duplicated page IDs (assuming it's stored as a comma-separated string)
  $duplicated_page_ids_string = get_field('duplicated_page_ids', $post_id);

  // Convert the string into an array
  $duplicated_page_ids = explode(',', $duplicated_page_ids_string);

  // Remove any empty or null values and trim any extra whitespace
  $duplicated_page_ids = array_filter(array_map('trim', $duplicated_page_ids));

  // If there are IDs to delete
  if (!empty($duplicated_page_ids)) {
    foreach ($duplicated_page_ids as $page_id) {
      wp_delete_post((int) $page_id, true); // Set second parameter to 'true' to bypass trash and permanently delete
    }
  }
}
add_action('wp_trash_post', 'delete_associated_pages_on_location_delete');
