<?php
function register_locations_cpt()
{

	/**
	 * Post Type: Locations.
	 */

	$labels = [
		"name" => "Locations",
		"singular_name" => "Location",
		"menu_name" => "Locations",
		"all_items" => "All Locations",
		"add_new" => "Add New",
		"add_new_item" => "Add New Location",
		"edit_item" => "Edit Location",
		"new_item" => "New Location",
		"view_item" => "View Location",
		"view_items" => "View Locations",
		"search_items" => "Search Locations",
		"not_found" => "No Locations found",
		"not_found_in_trash" => "No Locations found in bin",
		"parent" => "Parent Location:",
		"featured_image" => "Featured image for this Location",
		"set_featured_image" => "Set featured image for this Location",
		"remove_featured_image" => "Remove featured image for this Location",
		"use_featured_image" => "Use as featured image for this Location",
		"archives" => "Location archives",
		"insert_into_item" => "Insert into Location",
		"uploaded_to_this_item" => "Upload to this Location",
		"filter_items_list" => "Filter Locations list",
		"items_list_navigation" => "Locations list navigation",
		"items_list" => "Locations list",
		"attributes" => "Locations attributes",
		"name_admin_bar" => "Location",
		"item_published" => "Location published",
		"item_published_privately" => "Location published privately.",
		"item_reverted_to_draft" => "Location reverted to draft.",
		"item_scheduled" => "Location scheduled",
		"item_updated" => "Location updated.",
		"parent_item_colon" => "Parent Location:",
	];

	$args = [
		"label" => "Locations",
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"can_export" => false,
		"rewrite" => ["slug" => "locations", "with_front" => true],
		"query_var" => false,
		"menu_position" => 25,
		"menu_icon" => "dashicons-location-alt",
		"supports" => ["title", "thumbnail", "custom-fields"],
		"show_in_graphql" => false,
	];

	register_post_type("locations", $args);
}
add_action('init', 'register_locations_cpt');
