<?php
function add_external_scripts()
{
  $stylesheet_directory = get_stylesheet_directory_uri();

  // FAQ
  wp_enqueue_script('lb-faq-js', $stylesheet_directory . '/js/lb-faq.js', array('jquery'), '1.0.0', true);
  wp_enqueue_style('lb-faq-css', $stylesheet_directory . '/css/lb-faq.css', array(), '1.0.0');


  // Who's Involved
  wp_enqueue_script('whos-involved-js', $stylesheet_directory . '/js/whos-involved.js', array('jquery'), '1.0.0', true);
  wp_enqueue_style('whos-involved-css', $stylesheet_directory . '/css/whos-involved.css', array(), '1.0.0');


  // Nav menu add location to links
  wp_enqueue_script('nav-links-js', $stylesheet_directory . '/js/nav-locations-links.js', array('jquery'), '1.0.0', true);


  // How Does X Work
  wp_enqueue_script('howdoesxwork-js', $stylesheet_directory . '/js/how-does-x-work.js', array('jquery'), '1.0.0', true);
  wp_enqueue_style('howdoesxwork-css', $stylesheet_directory . '/css/how-does-x-work.css', array(), '1.0.0');


  // Header
  wp_enqueue_script('header-js', $stylesheet_directory . '/js/header-url-highlight.js', array('jquery'), '1.0.0', true);


  // Guides Sidebar Widget
  wp_enqueue_script('guide-widget-js', $stylesheet_directory . '/js/guides-categories-sidebar-widget.js', array('jquery'), '1.0.0', true);


  // Online Wills
  if (is_page('online-wills-start') || is_page('online-wills') || is_page('online-wills-contact') || is_page('online-wills-all-done') || is_page('online-wills-thank-you')) {
    if (is_page('online-wills'))
      wp_enqueue_script('wills-form-script', $stylesheet_directory . '/js/acf-wills-form.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('wills-form-css', $stylesheet_directory . '/css/acf-online-wills.css', array(), '1.0.0');
  }


  // Glossary
  if (is_page('glossary')) {
    wp_enqueue_script('glossary', $stylesheet_directory . '/js/glossary.js', array('jquery'), '1.0', true);
    $glossary_info = array(
      'glossary_url' => admin_url('admin-ajax.php')
    );
    wp_localize_script('glossary', 'glossary_object', $glossary_info);
  }

}
add_action('wp_enqueue_scripts', 'add_external_scripts');
