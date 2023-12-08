<?php
// Map Header
function contact_details_map_shortcode()
{
  $post_id = get_the_ID();

  $post_title = get_the_title($post_id);
  return '<h2 class="section-hdr-thin-bold-purple-large">PUTTING ' . esc_html($post_title) . '<br><span style="font-weight:900;">ON THE MAP</span></h2>';
}
add_shortcode('contact_details_map_header', 'contact_details_map_shortcode');


// Mailto and Tel Links
function contact_details_mailto_tel_shortcode()
{
  $post_id = get_the_ID();

  $email = get_field('email', $post_id);
  $phone = get_field('phone', $post_id);

  if ($email && $phone) {
    return '<a href="mailto:' . $email . '">' . $email . '</a><br><a title="Click to dial with Communicator" href="tel:+44' . preg_replace("/\s+/", "", $phone) . '">' . $phone . '</a>';
  }
  return '';
}
add_shortcode('contact_details_mailto_tel', 'contact_details_mailto_tel_shortcode');


// Hero Copy
function contact_details_hero_shortcode()
{
  $post_id = get_the_ID();

  $post_title = get_the_title($post_id);

  $output = '<h2 class="contact-hero-welcome">WELCOME TO LYONS BOWE ' . strtoupper(esc_html($post_title)) . '</h2>';
  $output .= '<h3 class="section-hdr-thin-bold-left-medium">GET IN TOUCH WITH<br><span style="font-weight:900;">YOUR ' . strtoupper(esc_html($post_title)) . ' SOLICITORS</span></h3>';

  return $output;
}
add_shortcode('contact_details_hero_text', 'contact_details_hero_shortcode');
