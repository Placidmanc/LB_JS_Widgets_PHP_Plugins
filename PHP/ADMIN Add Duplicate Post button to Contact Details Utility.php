<?php
// Create a duplicate post for 'contact-details'
function duplicate_contact_post_as_draft($post_id)
{
  $post = get_post($post_id);
  $current_user = wp_get_current_user();
  $new_post_author = $current_user->ID;

  if (isset($post) && $post != null) {
    $args = array(
      'comment_status' => $post->comment_status,
      'ping_status' => $post->ping_status,
      'post_author' => $new_post_author,
      'post_content' => $post->post_content,
      'post_excerpt' => $post->post_excerpt,
      'post_name' => $post->post_name,
      'post_parent' => $post->post_parent,
      'post_password' => $post->post_password,
      'post_status' => 'draft',
      'post_title' => $post->post_title . ' (Clone)',
      'post_type' => $post->post_type,
      'to_ping' => $post->to_ping,
      'menu_order' => $post->menu_order
    );

    $new_post_id = wp_insert_post($args);

    // Duplicate all post meta
    $post_meta = get_post_meta($post_id);
    foreach ($post_meta as $key => $values) {
      foreach ($values as $value) {
        add_post_meta($new_post_id, $key, $value);
      }
    }
  }
}

// Add 'Duplicate' link in post rows for 'contact-details'
function duplicate_contact_post_link($actions, $post)
{
  if ($post->post_type == 'contact-details') {
    $actions['duplicate'] = '<a
  href="' . wp_nonce_url('admin.php?action=duplicate_contact_as_draft_action&post=' . $post->ID, basename(__FILE__), 'duplicate_contact_nonce') . '"
  title="Duplicate this item" rel="permalink">Duplicate</a>';
  }
  return $actions;
}
add_filter('post_row_actions', 'duplicate_contact_post_link', 10, 2);


function duplicate_contact_as_draft_action()
{
  if (
    !(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) &&
      'duplicate_contact_as_draft_action' == $_REQUEST['action']))
  ) {
    wp_die('No post to duplicate has been supplied!');
  }

  if (!isset($_GET['duplicate_contact_nonce']) || !wp_verify_nonce($_GET['duplicate_contact_nonce'], basename(__FILE__))) {
    return;
  }

  $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
  duplicate_contact_post_as_draft($post_id);
  wp_redirect(admin_url('edit.php?post_type=contact-details'));
  exit;
}
add_action('admin_action_duplicate_contact_as_draft_action', 'duplicate_contact_as_draft_action');
