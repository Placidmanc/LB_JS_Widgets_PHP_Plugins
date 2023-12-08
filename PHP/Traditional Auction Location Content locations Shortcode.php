<?php
function acf_locations_traditional_auctions_shortcode($atts)
{
  global $wp;

  // Extract other shortcode attributes
  $attributes = shortcode_atts(
    array(
      'page' => '',
      'section' => '',
      'field' => '',
      'default' => ''
    ),
    $atts,
    'acf_locations'
  );

  // Get the path to find the location as the page isn't a CPT
  $path = $wp->request;
  $segments = explode('/', trim($path, '/'));

  // Access its properties like $location->post_title, $location->ID, etc.
  $location = find_location_based_on_current_url_home($segments);
  $location_id = $location->ID;

  $page = $attributes['page'];
  $section = $attributes['section'];
  $field = $attributes['field'];
  $default = $attributes['default'];

  $output = '';

  if ($location_id) {
    // Define the query for the 'locations' CPT
    $args = array(
      'post_type' => 'locations',
      'posts_per_page' => 1,
      'meta_query' => array(
        array(
          'key' => 'location',
          'value' => $location->post_title,
        ),
      ),
    );
    $query = new WP_Query($args);

    if ($page === 'traditional_auctions') {
      if ($section === 'hero') {
        if ($field === 'location') {
          $output .= '<p class="elementor-heading-title">WELCOME TO LYONS BOWE ' . get_field('location', $location_id, true) . '</p>';
        } else {
          if ($query->have_posts()) {
            while ($query->have_posts()) {
              $query->the_post();

              if (have_rows('traditional_auctions_hero', $location_id)) {
                while (have_rows('traditional_auctions_hero', $location_id)) {
                  the_row();

                  if ($field === 'title') {
                    $main_title_thin = get_sub_field('main_title_thin', $location_id, true);
                    $main_title_bold = get_sub_field('main_title_bold', $location_id, true);
                    $output .= '<h1 class="hdr-thin-bold purple">' . $main_title_thin . '<br><span style="font-weight:900;">' . $main_title_bold . '</span></h1>';

                  } else if ($field === 'copy') {
                    $copy = get_sub_field('copy', $location_id, true);
                    $output .= '<p class="elementor-heading-title upper">' . $copy . '</p>';

                  } else if ($field === 'contact') {
                    $copy = get_sub_field('hero_contact', $location_id, true);

                    $output .= '<p class="elementor-heading-title">' . $copy . '</p>';
                  }
                }
              }
            }
          }
        }
      } else if ($section === 'team') {
        if ($query->have_posts()) {
          while ($query->have_posts()) {
            $query->the_post();
            if (have_rows('traditional_auctions_team', $location_id)) {
              while (have_rows('traditional_auctions_team', $location_id)) {
                the_row();
                $main_title_bold = get_sub_field('main_title_bold', $location_id, true);
                $main_title_thin = get_sub_field('main_title_thin', $location_id, true);
                $output .= '<h2 class="section-hdr-bold-thin">' . $main_title_bold . '<br><span style="font-weight:300;">' . $main_title_thin . '</span></h2>';
              }
            }
          }
        }
      }
    }

    wp_reset_postdata(); // Reset the query
    return $output;
  } else {
    if ($page === 'traditional_auctions') {
      if ($section === 'hero') {
        if ($field === 'location') {
          $output .= '<p class="elementor-heading-title">' . $default . '</p>';
        } else if ($field === 'title') {
          $output .= $default;
        } else if ($field === 'copy') {
          $output .= '<p class="elementor-heading-title upper">' . $default . '</p>';
        }
      } else if ($section === 'team') {
        $output .= $default;
      }
      return $output;
    }
    return '<div>No matching locations found</div>';
  }
}
add_shortcode('acf_locations_traditional_auctions', 'acf_locations_traditional_auctions_shortcode');
