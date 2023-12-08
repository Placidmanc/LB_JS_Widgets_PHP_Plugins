<?php
$midnight = strtotime('tomorrow midnight', current_time('timestamp')); // Get the timestamp for the next midnight

// Schedule the event 
if (!wp_next_scheduled('delete_old_questionnaires')) {
  wp_schedule_event($midnight, 'daily', 'delete_old_questionnaires');
}

add_action('delete_old_questionnaires', 'delete_old_questionnaire_posts');
function delete_old_questionnaire_posts()
{
  $args = array(
    'post_type' => 'questionnaire',
    'date_query' => array(
      'before' => date('Y-m-d', strtotime('-90 days'))
    ),
    'posts_per_page' => -1,
    'fields' => 'ids'
  );

  $old_posts = get_posts($args);
  foreach ($old_posts as $post_id) {
    wp_delete_post($post_id, true);
  }

  // Send a notification email
  $to = 'paul@mediagrand.co.uk';
  $subject = 'Lyons Bowe: Delete Old Questionnaire Posts 90 Days';
  $message = 'Delete Old Questionnaire Posts 90 Days has run';

  $headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: Lyons Bowe <marketing@lyonsbowe.co.uk>',
  );

  // Send the email
  wp_mail($to, $subject, $message, $headers);
}
