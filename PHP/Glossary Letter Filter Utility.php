<?php
function filter_glossary()
{
  global $wpdb;

  // Sanitise the inputs
  $letter = isset($_POST['letter']) ? sanitize_text_field($_POST['letter']) : '';
  $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
  $specificTerm = isset($_POST['specificTerm']) ? sanitize_text_field($_POST['specificTerm']) : '';

  // Ensure the letter is only a single character
  if (!empty($letter) && strlen($letter) != 1 && $letter != "#") {
    die('Invalid request');
  }

  $args = array(
    'post_type' => 'glossary',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
  );

  if ($letter === "#") {
    // If the letter is "#", we want only entries where the title contains a number
    $querystr = "
            SELECT $wpdb->posts.* 
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_title REGEXP '[0-9]'
            AND $wpdb->posts.post_type = 'glossary'
            AND $wpdb->posts.post_status = 'publish'
            ORDER BY $wpdb->posts.post_title ASC
        ";
    $query_results = $wpdb->get_results($querystr, OBJECT);
  } else {
    if (!empty($letter)) {
      $args['title_like'] = $letter . '%';
    } else if (!empty($search)) {
      $args['s'] = $search;
    } else if (!empty($specificTerm)) {
      $args['title_like'] = substr($specificTerm, 0, 1) . '%';
    }

    $query = new WP_Query($args);
    $query_results = $query->posts;
  }

  $result = array();
  if ($query_results) {
    foreach ($query_results as $post) {
      setup_postdata($post);
      $term_name = get_post_field('post_name', $post->ID);
      $term_title = get_the_title($post->ID);

      // Fetch the term's definition and related terms for each term
      $definition = get_post_meta($post->ID, 'glossary_definition', true);
      $department = get_post_meta($post->ID, 'department', true);
      $related_terms_ids = get_post_meta($post->ID, 'related_terms', true);

      // Set default message
      $related_terms = array("No related terms");

      if (!empty($related_terms_ids)) {
        $related_terms = array(); 	// Reset the array now related terms are found
        foreach ($related_terms_ids as $related_term_id) {
          $related_term_name = get_post_field('post_name', $related_term_id);
          $related_term_title = get_the_title($related_term_id);
          $related_terms[] = array('term_name' => $related_term_name, 'term_title' => $related_term_title);
        }
      }

      $result[] = array(
        'term_name' => $term_name,
        'term_title' => $term_title,
        'definition' => $definition,
        'department' => $department,
        'related_terms' => $related_terms,
        'selected' => (!empty($specificTerm) && $specificTerm == $term_name) ? true : false,
      );
    }
  } else {
    // No results found
    $result[] = array(
      'term_name' => 'No results',
      'term_title' => 'No results found',
      'definition' => '',
      'department' => '',
      'related_terms' => array("No related terms"),
      'selected' => false,
    );
  }

  wp_reset_postdata();

  echo json_encode($result);

  die();
}
add_action('wp_ajax_filter_glossary', 'filter_glossary');
add_action('wp_ajax_nopriv_filter_glossary', 'filter_glossary');
