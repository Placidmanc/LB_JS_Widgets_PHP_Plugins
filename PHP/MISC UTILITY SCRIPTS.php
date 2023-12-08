<?php
// Archives : Remove prefixes from archive titles
function remove_avada_portfolio_title_prefix($title)
{
  if (is_post_type_archive('avada_portfolio')) {
    $title = single_term_title('', false);
  }
  if (is_post_type_archive('portfolio_tags')) {
    $title = single_term_title('', false);
  }
  if (is_post_type_archive('guides')) {
    $title = single_term_title('', false);
  }

  return $title;
}
add_filter('get_the_archive_title', 'remove_avada_portfolio_title_prefix');


// Glossary : Add '$title_like' filter
function title_like_posts_where($where, $query)
{
  global $wpdb;

  if ($title_like = $query->get('title_like')) {
    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql($title_like) . '\'';
  }

  return $where;
}
add_filter('posts_where', 'title_like_posts_where', 10, 2);


// Footer : Register Location Based Footer Menu
function register_location_menu()
{
  $registered_menus = get_registered_nav_menus();
  $menu_slug = 'location-specific-menu';

  if (!array_key_exists($menu_slug, $registered_menus)) {
    register_nav_menu($menu_slug, __('Locations Specific Menu'));
  }
}

add_action('init', 'register_location_menu');


// Portfolio : Sort portfolio (team) alphabetically by forename in WP Admin
add_action('pre_get_posts', function ($query) {
  if ($query->is_archive()) {
    if (get_query_var('post_type') == 'avada_portfolio') {
      $query->set('order', 'ASC');
      $query->set('orderby', 'title');
    };
  };
});
// Sort the team using 'the_team' custom query id (added in the template)
add_action('elementor/query/the_team', function ($query) {
  $query->set('order', 'ASC');
  $query->set('orderby', 'title');
});


// Blog : Remove category from Blog Recent Posts widget
function modify_widget()
{
  $r = array('category__not_in' => '334');
  return $r;
}
add_filter('widget_posts_args', 'modify_widget');

function remove_widget_categories($args)
{
  $exclude = "334";
  $args["exclude"] = $exclude;
  return $args;
}
add_filter("widget_categories_args", "remove_widget_categories");

function exclude_category_home($query)
{
  if ($query->is_home) {
    $query->set('cat', '-334');
  }
  return $query;
}
add_filter('pre_get_posts', 'exclude_category_home');
