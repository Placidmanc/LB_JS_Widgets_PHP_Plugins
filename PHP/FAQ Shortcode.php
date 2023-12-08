<?php
function create_faq_header($post_id)
{
  $main_title_bold = get_field('main_title_bold', $post_id);
  $main_title_thin = get_field('main_title_thin', $post_id);

  return '<h2 class="section-hdr-bold-thin">' . $main_title_bold . '<br><span style="font-weight:300;">' . $main_title_thin . '</span></h2>';
}

function create_faq_author_box($post_id)
{
  $author_image = get_field('author_image', $post_id);
  $author_name = get_field('author_name', $post_id);
  $author_role = get_field('author_role', $post_id);

  $output = '<div class="author-box">';
  $output .= '<img src="' . $author_image . '" />';
  $output .= '<div>';
  $output .= '<h3>ANSWERED BY</h3>';
  $output .= '<h4>' . $author_name . '</h4>';
  $output .= '<h5>' . $author_role . '</h4>';
  $output .= '</div>';
  $output .= '</div>';

  return $output;
}

function create_faq_content($post_id)
{
  $output = '<div>';

  $faqs = [];

  // Fetch all FAQs
  if (have_rows('faqs', $post_id)) {
    while (have_rows('faqs', $post_id)) {
      the_row();
      $faqs[] = [
        'question' => get_sub_field('faq_question'),
        'answer' => get_sub_field('faq_answer'),
        'desktop_position' => get_sub_field('desktop_column_position'),
        'tablet_position' => get_sub_field('tablet_column_position')
      ];
    }
  }

  // Desktop Columns
  $output .= '<div class="faq-columns desktop">';

  // if all faqs are in the left column, don't sort, split the faqs - less control over sorting with selected position
  if (check_faq_column($faqs, 'desktop_position')) {
    $faqs_count = count($faqs);
    $desktop_items = ceil($faqs_count / 3);

    for ($i = 0; $i < 3; $i++) {
      $output .= '<div class="faq-column desktop">';
      foreach (array_slice($faqs, $i * $desktop_items, $desktop_items) as $faq) {
        $output .= "<h3>{$faq['question']}</h3>";
        $output .= "<p>{$faq['answer']}</p>";
      }

      // Add static content in last position
      if ($i === 2)
        $output .= create_faq_static($post_id);

      $output .= '</div>';
    }
  } else {
    // Sort the FAQs
    $sorted_desktop_faqs = [
      'Left' => [],
      'Middle' => [],
      'Right' => []
    ];

    // Sorting FAQs based on their position
    foreach ($faqs as $faq) {
      $sorted_desktop_faqs[$faq['desktop_position']][] = $faq;
    }

    // Display the sorted FAQs
    foreach ($sorted_desktop_faqs as $position => $faqs_in_position) {
      $output .= '<div class="faq-column desktop">';
      foreach ($faqs_in_position as $faq) {
        $output .= "<h3>{$faq['question']}</h3>";
        $output .= "<p>{$faq['answer']}</p>";
      }
      // Add static content in last position
      if ($position === 'Right') {
        $output .= create_faq_static($post_id);
      }
      $output .= '</div>';
    }
  }
  $output .= '</div>'; // End faq-columns for desktop

  // Tablet Columns
  $output .= '<div class="faq-columns tablet">';

  // if all faqs are in the left column in admin, don't sort, split the faqs - less control over sorting with selected position
  if (check_faq_column($faqs, 'tablet_position')) {
    $faqs_count = count($faqs);
    $tablet_items = ceil($faqs_count / 2);

    for ($i = 0; $i < 2; $i++) {
      $output .= '<div class="faq-column tablet">';
      foreach (array_slice($faqs, $i * $tablet_items, $tablet_items) as $faq) {
        $output .= "<h3>{$faq['question']}</h3>";
        $output .= "<p>{$faq['answer']}</p>";
      }

      // Add static content in last position
      if ($i === 1)
        $output .= create_faq_static($post_id);

      $output .= '</div>';
    }
  } else {
    // Sort the FAQs
    $sorted_tablet_faqs = [
      'Left' => [],
      'Right' => []
    ];

    // Sorting FAQs based on their position
    foreach ($faqs as $faq) {
      $sorted_tablet_faqs[$faq['tablet_position']][] = $faq;
    }

    // Display the sorted FAQs
    foreach ($sorted_tablet_faqs as $position => $faqs_in_position) {
      $output .= '<div class="faq-column tablet">';
      foreach ($faqs_in_position as $faq) {
        $output .= "<h3>{$faq['question']}</h3>";
        $output .= "<p>{$faq['answer']}</p>";
      }

      // Add static content in last position
      if ($position === 'Right') {
        $output .= create_faq_static($post_id);
      }
      $output .= '</div>';
    }
  }

  $output .= '</div>'; // End faq-columns for tablet

  // Mobile Columns 
  $row_counter = 1;

  $output .= '<div class="faq-columns mobile">';
  foreach ($faqs as $faq) {
    $output .= '<div class="lb-faq-btn" id="lb-faq-btn-' . $row_counter . '">';
    $output .= '<div class="btn-content">';
    $output .= '<img src="/wp-content/uploads/2023/05/plus-btn.png" alt="plus icon" />';
    $output .= '<img src="/wp-content/uploads/2023/05/minus-btn-pink.png" alt="minus icon"  />';
    $output .= '<p>' . $faq['question'] . '</p>';
    $output .= '</div>';

    $display = ($row_counter < 2) ? 'block' : 'none';
    $output .= '<div class="lb-faq-content"  id="lb-faq-content-' . $row_counter . '" style="display: ' . $display . ';">';

    $output .= '<p>' . $faq['answer'] . '</p>';
    $output .= '</div>';
    $output .= '</div>';

    $row_counter++;
  }
  $output .= '</div>';	// End faq-columns for mobile
  $output .= '</div>'; 	// End of wrapper

  return $output;
}

function create_faq_static($post_id)
{
  $cta_header = get_field('cta_header', $post_id);
  $cta_text = get_field('cta_text', $post_id);
  $button_label = get_field('button_label', $post_id);
  $button_url = get_field('button_url', $post_id);

  $output = '<div class="cta-wrapper">';
  $output .= '<div class="cta-team">';
  $output .= '<img width="274" height="78" src="https://lyonsbowe.co.uk/wp-content/uploads/2023/05/family-law-team-white.webp" alt="the team" />';
  $output .= '</div>';
  $output .= '<div class="cta-inner">';
  $output .= '<h4>' . $cta_header . '</h4>';
  $output .= '<p>' . $cta_text . '</p>';
  $output .= '<a href="' . $button_url . '">';
  $output .= '<span>' . $button_label . '</span>';
  $output .= '</a>';
  $output .= '</div>';
  $output .= '</div>';

  return $output;
}

function check_faq_column($arr, $var)
{
  foreach ($arr as $faq) {
    if ($faq[$var] !== 'Left' && $faq[$var] !== false) {
      return false;
    }
  }
  return true;
}

function lbfaq_shortcode($atts)
{
  $attributes = shortcode_atts(
    array(
      'page' => ''
    ),
    $atts
  );

  if (empty($attributes['page'])) {
    return '';
  }

  $post = get_page_by_path($attributes['page'], OBJECT, 'faq');
  if (!$post) {
    $post = get_post($attributes['page']);
  }

  if (!$post || $post->post_type !== 'faq') {
    return '';
  }

  $output = '<div class="lb-faq-wrapper">';			// Start the content wrapper

  // Get the heading and author box
  $output .= '<div class="faq-header">';
  $output .= create_faq_header($post->ID);
  $output .= create_faq_author_box($post->ID);
  $output .= '</div>';	 // End faq-header

  $output .= create_faq_content($post->ID);	// Create the FAQs

  $output .= '</div>';	// End lb-faq-wrapper

  return $output;
}
add_shortcode('lbfaq', 'lbfaq_shortcode');
