<?php
class Guides_Widget extends WP_Widget
{
  function __construct()
  {
    parent::__construct(
      'guides_widget',
      'Guides Categories Widget',
      array('description' => 'Displays Guide categories on Guide archive and Guide category archive pages')
    );
  }

  public function widget($args, $instance)
  {
    $categories = get_terms(array(
      'taxonomy' => 'guides_categories',
      'parent' => 0
    ));

    echo $args['before_widget'];

    echo '<ul id="guides-list">';
    foreach ($categories as $category) {
      echo '<li class="guide-parent-category" id="parent-cat-' . $category->term_id . '">' . $category->name;
      $child_categories = get_terms(array(
        'taxonomy' => 'guides_categories',
        'parent' => $category->term_id
      ));

      if (!empty($child_categories)) {
        echo '<ul class="guide-child-category-list">';
        foreach ($child_categories as $child_category) {
          $term_link = get_term_link($child_category->term_id);
          if (!is_wp_error($term_link)) {
            echo '<li class="child-category"><a href="' . $term_link . '">' . $child_category->name . '</a></li>';
          }
        }
        echo '</ul>';
      }
      echo '</li>';
    }
    echo '</ul>';

    echo $args['after_widget'];
  }
}

function register_guides_widget()
{
  register_widget('Guides_Widget');
}
add_action('widgets_init', 'register_guides_widget');
