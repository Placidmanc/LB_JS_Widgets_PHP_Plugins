<?php
add_action('frontend_admin/save_post', 'get_questionnaire_fields', 20, 2);

function get_questionnaire_fields($form, $post_ID)
{
  // Don't run on WP's post save, only ACF's save.
  if ($post_ID === 'options') {
    return;
  }

  // Fetch the post object, check if it's a "questionnaire" post type.
  $post = get_post($post_ID);

  if ($post && $post->post_type === 'questionnaire') {
    // If the post is being updated, skip the duplication
    if ($post->post_date !== $post->post_modified) {
      //custom_e_log('###### Post is being updated, skipping ####');
      return;
    }

    // Any new fields that need to be included in the CSV file MUST be added to this array
    $fields_lookup = array(
      'urgent_will' => 'urgent_will',
      'full_name' => 'full_name',
      'email' => 'email',
      'phone_number' => 'phone_number',
      'address_line_1' => 'address_line_1',
      'address_line_2' => 'address_line_2',
      'town_city' => 'town_city',
      'postcode' => 'postcode',
      'relationship' => 'relationship',
      'family_lawyer' => 'family_lawyer',
      'probate_solicitor' => 'probate_solicitor',
      'dot' => 'dot',
      'children' => 'children',
      'protection_for_children' => 'protection_for_children',
      'protection_info' => 'protection_info',
      'own_property' => 'own_property',
      'live_england_wales' => 'live_england_wales',
      'where_do_you_live' => 'where_do_you_live',
      'everything_owned_in_uk' => 'everything_owned_in_uk',
      'where_are_assets' => 'where_are_assets',
      'own_a_business' => 'own_a_business',
      'donate' => 'donate',
      'time_to_call' => 'time_to_call',
      'time_to_call_other' => 'time_to_call_other',
    );

    // Fetch ACF fields here
    $fields = get_fields($post_ID);
    //custom_e_log('Fields: ' . print_r($fields, true));

    // Build CSV data array
    $csv_data = build_csv_data($fields, $fields_lookup);

    // Convert the selected fields array into CSV format
    $csv_content = array_to_csv($csv_data);

    // Save the CSV to a file and get the URL
    $file = save_csv_to_file($csv_content, $post_ID);

    // Send an email 
    send_csv_email($file);
  }
}

// Helper function to convert an array to CSV format
function array_to_csv($data)
{
  $fp = fopen('php://temp', 'w+'); // Open a temporary file in write mode

  // Add the header row with field names
  fputcsv($fp, array_keys($data));

  // Add the data row
  fputcsv($fp, $data);

  // Rewind the file pointer to the start of the file
  rewind($fp);

  // Read the file contents
  $csv = stream_get_contents($fp);

  // Close the file pointer
  fclose($fp);

  return $csv;
}

// Function to save the CSV content to a file
function save_csv_to_file($csv_content, $post_ID)
{
  // Get the uploads directory path
  $uploads = wp_upload_dir();
  $questionnaires_dir = $uploads['basedir'] . '/questionnaires';
  // $questionnaires_dir path e.g. /home/customer/www/staging6.lyonsbowe.co.uk/public_html/wp-content/uploads/questionnaires

  // Check if the directory exists, if not create it
  if (!file_exists($questionnaires_dir)) {
    wp_mkdir_p($questionnaires_dir);
  }

  // Generate the file name with a timestamp
  $timestamp = date('d-m-Y_H-i');
  $file_name = 'questionnaire-' . $post_ID . '-' . $timestamp . '.csv';
  $file_path = $questionnaires_dir . '/' . $file_name;
  // $file_path e.g. /home/customer/www/staging6.lyonsbowe.co.uk/public_html/wp-content/uploads/questionnaires/questionnaire-25833-23-11-2023_09-04.csv

  // Save the CSV content to the file
  file_put_contents($file_path, $csv_content);

  // Return the URL to the file 
  // $file_url e.g. https://staging6.lyonsbowe.co.uk/wp-content/uploads/questionnaires/questionnaire-25833-23-11-2023_09-04.csv
  $file_url = $uploads['baseurl'] . '/questionnaires/' . $file_name;

  return [$file_url, $file_path];
}

// Iterate over the lookup table and build the CSV data array
function build_csv_data($fields, $fields_lookup)
{
  $csv_data = array();

  // Initialize CSV data with headers from fields_lookup and empty values
  foreach ($fields_lookup as $csv_header) {
    $csv_data[$csv_header] = '';
  }

  // Recursive function to process fields
  function process_fields($sub_fields, &$csv_data, $fields_lookup)
  {
    foreach ($sub_fields as $key => $value) {
      // Check if this is another nested array and recurse
      if (is_array($value)) {
        process_fields($value, $csv_data, $fields_lookup);
      } else {
        // Process non-array fields
        if (array_key_exists($key, $fields_lookup)) {
          // Handle true/false values
          if (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
          }
          // Assign the value to the corresponding header in csv_data
          $csv_header = $fields_lookup[$key];
          $csv_data[$csv_header] = $value;
        }
      }
    }
  }

  // Process the fields
  process_fields($fields, $csv_data, $fields_lookup);

  return $csv_data;
}

// Function to send an email with the CSV file as an attachment and/or link
function send_csv_email($file)
{
  // Get the URL and file path
  $file_url = $file[0];
  $file_path = $file[1];

  $to = 'newbusiness@lyonsbowe.co.uk';
  $subject = 'Lyons Bowe Online Wills Questionnaire';
  $message = 'Please find the attached questionnaire CSV file.<br><br>';
  $message .= 'You can also download the questionnaire CSV file from <a href="' . $file_url . '">here</a>';

  $headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: Lyons Bowe <marketing@lyonsbowe.co.uk>',
    'Cc: paul@mediagrand.co.uk',
  );

  $attachments = array($file_path);

  // Send the email with the attachment
  wp_mail($to, $subject, $message, $headers, $attachments);
}

function custom_e_log($message, $override_logging = false)
{
  $log_errors = false;

  if ($log_errors || $override_logging) {
    error_log($message);
  }
}
