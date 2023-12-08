<?php
add_action('frontend_admin/save_post', 'get_urgent_wills_contact_fields', 20, 2);

function get_urgent_wills_contact_fields($form, $post_ID)
{
  // Don't run on WP's post save, only ACF's save.
  if ($post_ID === 'options') {
    return;
  }

  // Fetch the post object, check if it's a "urgent-wills-contact" post type.
  $post = get_post($post_ID);

  if ($post && $post->post_type === 'urgent-wills-contact') {
    // If the post is being updated, skip the duplication
    if ($post->post_date !== $post->post_modified) {
      custom_e_log('###### Post is being updated, skipping ####');
      return;
    }

    // Fetch fields
    $name = get_field('name', $post_ID);
    $email = get_field('email', $post_ID);
    $telephone = get_field('telephone_number', $post_ID);
    $comment = get_field('message', $post_ID);

    // Compose the email subject and message
    $subject = 'Urgent Wills Contact Form';
    $message = '<p>' . $name . '</p>';
    $message .= '<p>' . $email . '</p>';
    $message .= '<p>' . $telephone . '</p>';
    $message .= '<p>' . $comment . '</p>';

    // Set the recipient email address
    $to = 'newbusiness@lyonsbowe.co.uk';

    // Set additional headers to specify HTML content type
    $headers = array(
      'Content-Type: text/html; charset=UTF-8',
      'From: Lyons Bowe <marketing@lyonsbowe.co.uk>',
      'Cc: paul@mediagrand.co.uk',
    );

    // Send the email
    wp_mail($to, $subject, $message, $headers);
  }
}
