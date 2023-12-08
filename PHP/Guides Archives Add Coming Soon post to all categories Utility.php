<?php
/* 19953 is ID of the 'Coming Soon' post */
function add_coming_soon_post($query)
{
  if (!is_admin() && $query->is_main_query()) {

    if (is_post_type_archive('guides') || (is_tax('guides_categories'))) {
      // Exclude the 'Coming Soon' post initially
      $coming_soon_id = 19953;
      $excluded_posts = $query->get('post__not_in');
      $excluded_posts[] = $coming_soon_id;
      $query->set('post__not_in', $excluded_posts);
    }
  }
}
add_action('pre_get_posts', 'add_coming_soon_post');

function prepend_coming_soon_post($posts, $query)
{
  if (!is_admin() && $query->is_main_query()) {

    if (is_post_type_archive('guides') || (is_tax('guides_categories'))) {
      $coming_soon_post = get_post(19953);
      array_unshift($posts, $coming_soon_post);
    }
  }
  return $posts;
}
add_filter('the_posts', 'prepend_coming_soon_post', 10, 2);
