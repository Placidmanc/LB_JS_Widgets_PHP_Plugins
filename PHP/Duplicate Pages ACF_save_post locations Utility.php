<?php
add_action('acf/save_post', 'duplicate_pages_for_new_location', 20);

// Array to hold new page ID's
$new_ids = [];

function duplicate_pages_for_new_location($post_ID)
{
  $home_page_to_duplicate_id = 23534; 		// Home page ID to duplicate

  // Don't run on WP's post save, only ACF's save.
  if ($post_ID === 'options') {
    return;
  }

  // Fetch the post object, check if it's a "locations" post type.
  $post = get_post($post_ID);

  //custom_log('###### duplicate_pages_for_new_location #### WHERE $post->post_type=' . $post->post_type);

  if ($post && $post->post_type === 'locations') {
    // If the post is being updated, skip the duplication
    if ($post->post_date !== $post->post_modified) {
      custom_log('###### Post is being updated, skipping duplication ####');
      return;
    }
    //custom_log('###### duplicate_pages_for_new_location triggered #### WHERE $post_ID=' . $post_ID);

    // Temporarily remove the action to prevent it firing again
    remove_action('publish_locations', 'duplicate_pages_for_new_location');

    $args = [
      'name' => 'locations',
      'post_type' => 'page',
      'numberposts' => 1
    ];
    $root_page_query = new WP_Query($args);
    $root_page_id = 0;

    if ($root_page_query->have_posts()) {
      while ($root_page_query->have_posts()) {
        $root_page_query->the_post();
        $root_page_id = get_the_ID();
      }
      wp_reset_postdata();
    }

    $location_name = get_the_title($post_ID);
    //custom_log('Location name is: ' . $location_name);

    // Pages to duplicate
    $original_pages = array(
      'services' => array(
        '_id' => 11,
        'property-law' => array(
          '_id' => 15001,
          'buying-property' => 15444,
          'selling-a-property' => 15771,
          'new-build' => 15789,
          'modern-auction' => 20219,
          'traditional-auction' => 18467,
          'transfer-of-equity' => 20032,
          'remortgage' => 20170,
        ),
        'wills-probate' => array(
          '_id' => 13141,
          'local-will-solicitors' => 14415,
          'make-an-lpa' => 14716,
          'probate-solicitors' => 14208,
        ),
        'family-law' => array(
          '_id' => 12449,
          'divorce-lawyers' => 12935,
          'child-arrangements' => 13378,
          'financial-arrangements' => 13380,
        ),
      ),
      //'contact' => 21017,
    );


    // Chunk the original pages array into smaller arrays of 5 elements each
    $chunked_original_pages = array_chunk($original_pages, 5, true);

    if ($location_name && $root_page_id) {

      /******** Start the duplication *********/

      //custom_log('$home_page_to_duplicate_id: ' . $home_page_to_duplicate_id);

      // Create '{location} home' as a child of 'locations'
      $location_home_id = duplicate_page($home_page_to_duplicate_id, $location_name, $root_page_id, $post_ID, $home_page_to_duplicate_id, true);

      add_new_page_id($location_home_id);

      // Change slug to the name of the location
      wp_update_post(
        array(
          'ID' => $location_home_id,
          'post_name' => $location_name
        )
      );

      foreach ($chunked_original_pages as $chunk) {
        duplicate_page_hierarchy($chunk, $location_home_id, $location_name, $post_ID, $home_page_to_duplicate_id);

        // Sleep between each chunk to avoid 504 Bad Gateway timeouts
        sleep(3);
      }

      // Save $new_ids to 'duplicated_page_ids' field
      save_new_page_ids($post_ID);

      if (is_wp_error($post_ID)) {
        custom_log('POST UPDATED ERROR: ' . $post_ID);
      } else {
        custom_log('POST UPDATED SUCCESS: ' . $post_ID);
      }

      // Re-add the action
      add_action('publish_locations', 'duplicate_pages_for_new_location', 10, 2);
    } else {
      echo 'Location page does not exist';
    }
  }
}

function add_new_page_id($new_page_id)
{
  global $new_ids; // Use the global variable $new_ids

  //custom_log('####  $new_page_id=' . $new_page_id);

  if (!is_array($new_ids) || empty($new_ids)) {
    $new_ids = []; // Initialise the array if it's not an array or is empty
  }

  $new_ids[] = $new_page_id; // Add the new page ID to the array
  $new_ids_str = implode(', ', $new_ids);
  //custom_log('####  $new_ids=' . $new_ids_str);
}

function save_new_page_ids($post_ID)
{
  global $new_ids; // Use the global variable $new_ids

  //custom_log('####  save_new_page_ids');

  $new_duplicated_ids = implode(',', $new_ids);
  update_acf_field('field_65393bbaddbb1', $new_duplicated_ids, $post_ID, " ALL Pages Ids");
}

function duplicate_page_hierarchy($pages, $parent_id, $location_name = '', $post_ID, $home_page_to_duplicate_id)
{
  foreach ($pages as $page_title => $page_data) {

    if (is_array($page_data)) {
      $original_page_id = $page_data['_id'];
      $new_page_id = duplicate_page($original_page_id, $location_name, $parent_id, $post_ID, $home_page_to_duplicate_id);

      // Add new page ID 
      add_new_page_id($new_page_id);

      // Remove the '_id' key to iterate through child pages
      unset($page_data['_id']);

      // Recursive call to handle child pages
      duplicate_page_hierarchy($page_data, $new_page_id, $location_name, $post_ID, $home_page_to_duplicate_id);

    } else {
      $original_page_id = $page_data;
      $new_page_id = duplicate_page($original_page_id, $location_name, $parent_id, $post_ID, $home_page_to_duplicate_id);

      // Add new page ID
      add_new_page_id($new_page_id);
    }

    // Sleep to prevent 504 Bad Gateway timeouts
    sleep(3);
  }
}

function duplicate_page($original_page_id, $title_prefix, $parent_id, $post_ID, $home_page_to_duplicate_id, $is_root = false)
{

  $original_page = get_post($original_page_id);

  if (!($original_page instanceof WP_Post)) {
    return;
  }

  $new_title = $title_prefix ? ($title_prefix . ' - ' . $original_page->post_title) : $original_page->post_title;
  //custom_log("SET new_title: " . $new_title);

  // Update title if it's a root page
  if ($is_root) {
    $new_title = $title_prefix . ' - Home'; // This will result in '{location} - Home'
  }

  $new_slug = $original_page->post_name;

  $new_page_id = wp_insert_post([
    'post_title' => $new_title,
    'post_name' => $new_slug,
    'post_content' => $original_page->post_content,
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_parent' => $parent_id,
  ]);

  //custom_log("NEW PAGE IS: " . $new_page_id);

  if ($new_page_id) {
    $acf_lookup = [
      'property-law' => ["field_652fb7f07877a", "field_652fb7e77876a"],
      'buying-property' => ["field_652fb7ef78779", "field_652fb7e678769"],
      'selling-a-property' => ["field_652fb7ef78778", "field_652fb7e678768"],
      'new-build' => ["field_652fb7ee78777", "field_652fb7e578767"],
      'modern-auction' => ["field_652fb7ee78776", "field_652fb7e578766"],
      'traditional-auction' => ["field_652fb7ed78775", "field_652fb7e578765"],
      'transfer-of-equity' => ["field_652fb7ed78774", "field_652fb7e478764"],
      'remortgage' => ["field_652fb7ec78773", "field_652fb7e478763"],
      'wills-probate' => ["field_652fb7ec78772", "field_652fb7e378762"],
      'local-will-solicitors' => ["field_652fb7eb78771", "field_652fb7e378761"],
      'make-an-lpa' => ["field_652fb7eb78770", "field_652fb7e278760"],
      'probate-solicitors' => ["field_652fb7ea7876f", "field_652fb7e27875f"],
      'family-law' => ["field_652fb7ea7876e", "field_652fb7e17875e"],
      'divorce-lawyers' => ["field_652fb7ea7876d", "field_652fb7e17875d"],
      'child-arrangements' => ["field_652fb7e97876c", "field_652fb7e07875c"],
      'financial-arrangements' => ["field_652fb7e87876b", "field_652fb7df7875b"],
    ];

    $meta_data = get_post_meta($original_page_id);
    foreach ($meta_data as $key => $value) {
      update_post_meta($new_page_id, $key, maybe_unserialize($value[0]));
    }

    if ($is_root) {
      //custom_log("- is_root TEST: " . $is_root);
      //custom_log("- Trying to update post ID: " . $new_page_id);

      // home
      update_acf_field("field_65140ed27b438", $title_prefix, $post_ID, "Location"); // not in locations meta 
      update_acf_field("field_652d18a43ab39", true, $post_ID, "Is Duplicate"); // in locations meta
      update_acf_field("field_652d18bb3ab3a", $home_page_to_duplicate_id, $post_ID, "Page Id"); // in locations meta

      // Update the locations ACF fields
      update_location_strings($post_ID, 'home', $title_prefix);
    } else {
      if (array_key_exists($new_slug, $acf_lookup)) {
        $fields = $acf_lookup[$new_slug];
        update_acf_field($fields[0], true, $post_ID, $new_slug . " Is Duplicate");
        update_acf_field($fields[1], $original_page_id, $post_ID, $new_slug . " Page Id");

        // Update the locations ACF fields
        update_location_strings($post_ID, $new_slug, $title_prefix);
      }
    }

    //custom_log('PAGE DUPLICATED in duplicate_page(): ' . $new_title . ', OPID ' . $original_page_id);

    // Update Yoast SEO meta
    update_yoast_meta($new_page_id, $title_prefix, $original_page_id);
  }

  return $new_page_id;
}

function update_yoast_meta($new_page_id, $location_name, $original_page_id)
{
  $yoast_title = get_post_meta($original_page_id, '_yoast_wpseo_title', true);
  $yoast_meta_desc = get_post_meta($original_page_id, '_yoast_wpseo_metadesc', true);

  // Append location name to Yoast SEO fields
  if ($yoast_title) {
    update_post_meta($new_page_id, '_yoast_wpseo_title', $yoast_title . ' | ' . $location_name);
  }
  if ($yoast_meta_desc) {
    update_post_meta($new_page_id, '_yoast_wpseo_metadesc', $yoast_meta_desc . ' | ' . $location_name);
  }
}

function update_acf_field($field_key, $value, $new_page_id, $description, $is_testing = false)
{
  //custom_log('-- UPDATE VARS : $value=' . $value . ' : $new_page_id= ' . $new_page_id . ' : $description= ' . $description);

  $updated = update_field($field_key, $value, $new_page_id);

  //custom_log('-- SET ' . $description . ' - Updated: ' . ($updated ? "true" : "false"));
  if ($is_testing) {
    test_acf_field($field_key, $value, $new_page_id, $description);
  }

}

function test_acf_field($field_key, $value, $new_page_id, $description)
{
  $actual_value = get_field($field_key, $new_page_id);

  //custom_log('-- TEST VARS: $value=' . $value . ' : $new_page_id=' . $new_page_id . ' : $description=' . $description . ' : $actual_value=' . $actual_value . ' : gettype($actual_value)=' . gettype($actual_value));

  if ($actual_value === $value) {
    custom_log('-- TEST ' . $description . ' - Update confirmed. Value: ' . $actual_value);
  } else {
    custom_log('-- TEST ' . $description . ' - Update failed or didn\'t take effect. Actual value: ' . $actual_value);
  }
}

function update_location_strings($post_id, $page_slug, $new_location_name)
{
  //custom_log('## update_location_strings: $post_id=' . $post_id . ', $page_slug=' . $page_slug . ', $new_location_name=' . $new_location_name);

  // Define the structure for fields and sub-fields
  $fields_to_update = [
    'home' => [
      [
        'field_6526694762adb',
        // home_hero
        ['field_65300334d6fb9', 'SHEPTON MALLET SOLICITORS'],
        // main_title_bold
        ['field_6526694762ade', 'At Lyons Bowe we believe that you should have a choice and a say in how you access legal services. That’s why we’re on a mission to make a better law firm. A firm with the convenience of the SHEPTON MALLET high street and the innovation of the city.']
        // copy
      ],
      [
        'field_6526692562ad9',
        // home_services
        ['field_653002b01d009', 'SHEPTON MALLET’S']
        // main_title_bold
      ],
    ],
    'property-law' => [
      [
        'field_65266a0e62ae0',
        // property_hero
        ['field_6530ebf6ed037', 'YOUR SHEPTON MALLET'],
        // main_title_thin
        ['field_65266a0e62ae3', 'WHETHER YOU’RE A FIRST TIME BUYER, LADDER CLIMBER, OR PROPERTY MOGUL, OUR SHEPTON MALLET PROPERTY SOLICITORS ARE HERE TO HELP YOU TO TAKE THE NEXT STEPS IN YOUR PROPERTY JOURNEY']
        // copy
      ],
      [
        'field_65266a3462ae4',
        // property_services
        ['field_6530eceeb7929', 'SHEPTON MALLET']
        // main_title_thin
      ],
      [
        'field_65266a4e62ae6',
        // property_team
        ['field_6530ee1cb792c', 'SHEPTON MALLET TEAM'],
        // main_title_thin
      ],
    ],
    'buying-property' => [
      [
        'field_65266c0c3c3f2',
        // buying_hero
        ['field_6530f0ac54c1f', 'IN SHEPTON MALLET'],
        // main_title_bold
        ['field_65266c0c3c3f5', 'WHETHER YOU’RE A FIRST TIME BUYER, LADDER CLIMBER, OR PROPERTY MOGUL, OUR SHEPTON MALLET PROPERTY SOLICITORS ARE HERE TO HELP YOU ON YOUR WAY TO THE NEXT RUNG ON THE PROPERTY LADDER.']
        // copy
      ],
      [
        'field_65266c1a3c3f6',
        // buying_how_does
        ['field_6530f0ff1a9b9', 'WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_65266c253c3f8',
        // buying_team
        ['field_6530f1401a9ba', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'selling-a-property' => [
      [
        'field_65266b333c3e7',
        // selling_hero
        ['field_6530efb73371b', 'IN SHEPTON MALLET'],
        // main_title_bold
        ['field_65266b333c3ea', 'WHETHER YOU’RE A FIRST TIME SELLER, LADDER CLIMBER, OR PROPERTY MOGUL, OUR SHEPTON MALLET NEW BUILD SOLICITORS ARE HERE TO HELP YOU TO TAKE THE NEXT STEPS IN YOUR PROPERTY JOURNEY.']
        // copy
      ],
      [
        'field_65266b573c3eb',
        // selling_how_does
        ['field_6530f02a3371c', 'WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_65266ba33c3ef',
        // selling_team
        ['field_6530f0653371e', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'new-build' => [
      [
        'field_65143b7b6480d',
        // new_build_hero
        ['field_6530ee9fb792e', 'SHEPTON MALLET'],
        // main_title_bold
        ['field_65143cba64814', 'WHETHER YOU’RE A FIRST TIME SELLER, LADDER CLIMBER, OR PROPERTY MOGUL, OUR SHEPTON MALLET NEW BUILD SOLICITORS ARE HERE TO HELP YOU TO TAKE THE NEXT STEPS IN YOUR PROPERTY JOURNEY.']
        // copy
      ],
      [
        'field_6526653d02235',
        // new_build_how_does
        ['field_6530ef09b7930', 'WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_65266b8a3c3ed',
        // new_build_team
        ['field_6530ef56b7932', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'traditional-auction' => [
      [
        'field_65266e003c3fb',
        // traditional_auctions_hero
        ['field_6530f1dd1a9bc', 'IN SHEPTON MALLET'],
        // main_title_bold
        ['field_65266e003c3fe', 'Auction properties are an excellent way to buy an investment property, or buy a property at a bargain price. FIND OUT EVERYTHING YOU NEED TO KNOW ABOUT THE TRADITIONAL METHOD OF AUCTION IN SHEPTON MALLET.']
        // copy
      ],
      [
        'field_65266e133c3ff',
        // traditional_auctions_whos_involved
        ['field_6530f2b739e6e', 'IN A SHEPTON MALLET AUCTION?']
        // main_title_thin
      ],
      [
        'field_65266e1a3c401',
        // traditional_auctions_team
        ['field_6530f30239e70', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'modern-auction' => [
      [
        'field_6530f36239e72',
        // modern_auctions_hero
        ['field_6530f36239e74', 'IN SHEPTON MALLET'],
        // main_title_bold
        ['field_6530f36239e75', 'Auction properties are an excellent way to buy an investment property, or buy a property at a bargain price. FIND OUT EVERYTHING YOU NEED TO KNOW ABOUT THE TRADITIONAL METHOD OF AUCTION IN SHEPTON MALLET.']
        // copy
      ],
      [
        'field_6530f38239e76',
        // modern_auctions_whos_involved
        ['field_6530f38239e78', 'IN A SHEPTON MALLET AUCTION?']
        // main_title_thin
      ],
      [
        'field_6530f39739e79',
        // modern_auctions_team
        ['field_6530f39739e7b', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'transfer-of-equity' => [
      [
        'field_65266eeb30127',
        // transfer_of_equity_hero
        ['field_6530f4dc81132', 'TRANSFER EQUITY IN SHEPTON MALLET'],
        // main_title_bold
        ['field_65266eeb3012a', 'Changing the legal ownership of a property BY MOVING EQUITY is known as a Transfer of Equity. Our expert equity solicitors and leading tech are here to support you and your transferee from start to finish.']
        // copy
      ],
      [
        'field_65266f083012b',
        // transfer_of_equity_how_does
        ['field_6530f58b81134', 'WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_65266f253012d',
        // transfer_of_equity_team
        ['field_6530f5ba81135', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'remortgage' => [
      [
        'field_6526702dcc160',
        // remortgage_hero
        ['field_6530f61381137', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_6526702dcc163', 'Remortgaging a property can be a good way to save money, get some extra funds to re-decorate, or enable you to rent your home out to private tenants. Our SHEPTON MALLET remortgaging solicitors will be here to help and support you through every step of the journey.']
        // copy
      ],
      [
        'field_6526704acc164',
        // remortgage_how_can
        ['field_6530f6838113a', 'YOUR SHEPTON MALLET REMORTGAGE']
        // main_title_thin
      ],
      [
        'field_65267067cc166',
        // remortgage_team
        ['field_6530f6ad8113b', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'wills-probate' => [
      [
        'field_65267199eee66',
        // wills_probate_hero
        ['field_6530fa608113d', 'SHEPTON MALLET WILLS &'],
        // main_title_thin
        ['field_65267199eee69', 'At Lyons Bowe we believe that MAKING A WILL DOESN’T HAVE TO BE A DAUNTING EXPERIENCE. OUR SHEPTON MALLET SOLICITORS ARE ALWAYS INNOVATING THE WAY THAT YOU MAKE A WILL, TO MAKE IT CLEARER, EASIER, AND SMARTER.']
        // copy
      ],
      [
        'field_652671aeeee6a',
        // wills_probate_services
        ['field_6530fab38113f', 'SHEPTON MALLET']
        // main_title_thin
      ],
      [
        'field_652671c7eee6c',
        // wills_probate_team
        ['field_6530faf681142', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'local-will-solicitors' => [
      [
        'field_652672981e7d7',
        // wills_hero
        ['field_6530fb6081143', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_652672981e7da', 'At Lyons Bowe we believe that MAKING A WILL DOESN’T HAVE TO BE A DAUNTING EXPERIENCE. THAT’S WHY OUR SHEPTON MALLET WILL SOLICITORS ARE INNOVATING THE WAY THAT YOU MAKE A WILL, TO MAKE IT CLEARER, EASIER, AND SMARTER.']
        // copy
      ],
      [
        'field_652672d11e7dd',
        // wills_making
        ['field_6530fbd281146', 'SHEPTON MALLET']
        // main_title_bold
      ],
      [
        'field_652672ba1e7db',
        // wills_how_does
        ['field_6530fc0e81148', 'WILL WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_652672fc1e7df',
        // wills_emergency
        ['field_6530fc448114a', 'WILLS IN SHEPTON MALLET']
        // main_title_bold
      ],
      [
        'field_6526731c1e7e1',
        // wills_team
        ['field_6530fc7a8114b', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'make-an-lpa' => [
      [
        'field_6526793bd4cb5',
        // lpa_hero
        ['field_6530fde281156', 'SHEPTON MALLET'],
        // main_title_bold
        ['field_6526793bd4cb8', 'OUR LASTING POWER OF ATTORNEY SOLICITORS IN SHEPTON MALLET ARE AVAILABLE WHEREVER YOU ARE TO HELP YOU MAKE A HEALTH & WELFARE, OR PROPERTY & FINANCE LPA.']
        // copy
      ],
      [
        'field_65267959d4cb9',
        // lpa_how_does
        ['field_6530fe4081158', 'WORK IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_6526797d1e02c',
        // lpa_team
        ['field_6530fe6881159', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'probate-solicitors' => [
      [
        'field_652673c8e1c33',
        // probate_hero
        ['field_6530fcd48114e', 'IN SHEPTON MALLET'],
        // main_title_bold
        ['field_652673c8e1c36', 'WE’RE COMMITTED TO MAKING DEALING WITH THE DEATH OF A LOVED ONE EASIER AND MORE SIMPLE. OUR PROBATE SOLICITORS IN SHEPTON MALLET ARE HERE BY YOUR SIDE TO HELP MAKE THE NEXT STEPS AS COMFORTABLE AS POSSIBLE.']
        // copy
      ],
      [
        'field_6526781eb2f77',
        // probate_our_approach
        ['field_6526781eb2f7a', 'At Lyons Bowe Solicitors, we know that talking about death is never the easiest thing. Our Shepton Mallet probate solicitors are here to help make dealing with death, that bit easier.']
        // copy
      ],
      [
        'field_652678c014358',
        // probate_how_to_get
        ['field_6530fd5a81150', 'IN SHEPTON MALLET?']
        // main_title_bold
      ],
      [
        'field_652678df1435a',
        // probate_team
        ['field_6530fd9281153', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'family-law' => [
      [
        'field_652679d3fef4d',
        // family_hero
        ['field_6530feee0a3c4', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_652679d3fef50', 'WHETHER YOU ARE WANTING TO PREPARE FOR YOUR FUTURE WITH A COHABITATION OR PRE-NUPTIAL AGREEMENT, OR ENDING A MARRIAGE OR CIVIL PARTNERSHIP, YOUR SHEPTON MALLET FAMILY LAWYERS ARE RIGHT BY YOUR SIDE.']
        // copy
      ],
      [
        'field_652679f6fef51',
        // family_help
        ['field_6530ff6a0a3c6', 'SHEPTON MALLET']
        // main_title_thin
      ],
      [
        'field_65267a10fef53',
        // family_team
        ['field_6530ff980a3c9', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'divorce-lawyers' => [
      [
        'field_65267a6efef56',
        // divorce_hero
        ['field_6530ffc60a3ca', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_65267a6efef59', 'We believe in a collaborative, optimistic approach to getting divorced. Where children are involved, they will always remain our number one priority. Our SHEPTON MALLET divorce solicitors are here to help you throughout your divorce, on your terms.']
        // copy
      ],
      [
        'field_65267a8dfef5a',
        // divorce_how_does
        ['field_6531002f0a3cd', 'DIVORCE IN SHEPTON MALLET WORK?']
        // main_title_bold
      ],
      [
        'field_65267aa4fef5c',
        // divorce_help
        ['field_6531005c0a3ce', 'SHEPTON MALLET FAMILY']
        // main_title_thin
      ],
      [
        'field_65267ab9fef5e',
        // divorce_team
        ['field_6531009e0a3d0', 'MEET YOUR SHEPTON MALLET']
        // main_title_bold
      ],
    ],
    'child-arrangements' => [
      [
        'field_65267bcdfef6a',
        // child_hero
        ['field_653100f488f21', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_65267bcdfef6c', 'A CHILD ARRANGEMENT ORDER IS AN ORDER GIVEN BY THE COURT THAT LAYS OUT RESPONSIBILITIES FOR A CHILD. OUR SOLICITORS ARE HERE TO HELP YOU REACH AN ARRANGEMENT THAT MEETS YOUR FAMILY’S NEEDS.']
        // copy
      ],
      [
        'field_6531023488f25',
        // child_help
        ['field_6531023588f26', 'SHEPTON MALLET']
        // main_title_thin
      ],
      [
        'field_65267beffef70',
        // child_team
        ['field_6531028088f29', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
    'financial-arrangements' => [
      [
        'field_65267afcfef61',
        // financial_hero
        ['field_653102d288f2b', 'SHEPTON MALLET'],
        // main_title_thin
        ['field_65267afcfef64', 'A financial arrangement is an arrangement between you and your spouse regarding your finances. OUR SHEPTON MALLET SOLICITORS ARE HERE TO HELP YOU TO REACH A FINANCIAL ARRANGEMENT THAT MEETS YOUR FAMILY’S NEEDS.']
        // copy
      ],
      [
        'field_653101fd88f22',
        // financial_help
        ['field_653101fd88f23', 'SHEPTON MALLET']
        // main_title_thin
      ],
      [
        'field_65267b51fef67',
        // financial_team
        ['field_6531032688f2d', 'SHEPTON MALLET TEAM']
        // main_title_thin
      ],
    ],
  ];

  // Check if the page slug exists in our array
  if (array_key_exists($page_slug, $fields_to_update)) {
    $fields = $fields_to_update[$page_slug];

    // Loop through each field and sub-field, setting the new values
    foreach ($fields as $field_array) {
      $field_name = $field_array[0];
      $sub_fields = array_slice($field_array, 1);
      $field_value = [];

      foreach ($sub_fields as $sub_field_info) {
        [$sub_field_name, $string_to_replace] = $sub_field_info;
        //custom_log('####  $string_to_replace=' . $string_to_replace);

        $updated_value = str_ireplace('SHEPTON MALLET', $new_location_name, $string_to_replace);
        $field_value[$sub_field_name] = $updated_value;

        //custom_log('####  $field_value=' . $field_value);
      }

      // Update the field
      update_acf_field($field_name, $field_value, $post_id, 'FIELD=' . $sub_field_name);
    }
  }
}

function custom_log($message, $override_logging = false)
{
  $log_errors = false;

  if ($log_errors || $override_logging) {
    error_log($message);
  }
}

