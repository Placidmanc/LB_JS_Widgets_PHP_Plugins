<?php
/* Custom Glossary shortcode
 * 
 * The glossary is made up of the following scripts:
 * 	1.	{WP content}/{theme}/js/glossary.js - handles the search and adds functionality to the UI components (Access via Siteground > Website > File Manager)
 * 	2.	glossary_filter_shortcode() - in code snippets - draws the UI and provides the layout and placeholders for search results
 * 	3.	filter_glossary() - in code snippets - performs the WP search and returns the JSON data to the UI
 * 	4. 	title_like_posts_where( $where, $query ) - in code snippets - allows the use of like in the WP query
 * 	5. 	enqueue_glossary_script() - in code snippets - registers the glossary.js script with WP
 * 
 ** The page does not use an Elementor template, just a shortcode in the page builder.**
 ** The CSS is in the Avada page builder for the glossary page (black main page bar > Custom CSS).**
 */

function glossary_filter_shortcode()
{

  $output = '<div class="g-wrapper-full">';
  $output .= '<div class="g-wrapper-boxed">';
  $output .= '<div class="g-box-left">';
  $output .= '<h3 class="g-jargon">JARGON BUSTER</h3>';
  $output .= '<h1 class="g-main-title">LEGAL <span style="font-weight:900;">GLOSSARY</span></h1>';

  // Search bar
  $output .= '<input type="text" class="g-search" id="glossary-search" placeholder="Search for a word...">';

  $output .= '<H3 class="g-pick">PICK A LETTER</H3>';

  // Letter buttons
  $output .= '<div id="letter-filter" class="letter-buttons-box">';

  // Create button for numbers 
  $output .= '<button class="letter-button" data-letter="#">#</button>';

  // Create letter buttons
  foreach (range('A', 'Z') as $letter) {
    $output .= '<button class="letter-button" data-letter="' . $letter . '">' . $letter . '</button>';
  }

  $output .= '</div>';
  $output .= '<div class="g-terms-list">';

  // Terms lists
  $output .= '<ul id="first-list" class="g-terms"></ul>';
  $output .= '<ul id="second-list" class="g-terms"></ul>';

  $output .= '</div>';
  $output .= '</div>';

  $output .= '<div class="g-box-right">';
  $output .= '<div class="g-definition">';
  $output .= '<div class="g-def-top">';
  $output .= '<h3 class="g-def-top-label">Define:</h3>';

  // Term department 
  $output .= '<p class="g-dept" id="term-dept"></p>';
  $output .= '</div>';

  // Term title
  $output .= '<h3 class="g-def-term" id="term-title"></h3>';

  // Term definition
  $output .= '<p class="g-def-desc" id="term-definition"></p>';

  $output .= '<div class="g-def-related">';
  $output .= '<h2 class="g-related-hdr">RELATED<br><span style="font-weight:300;">TERMS</span></h2>';
  $output .= '<div class="g-related-terms-list">';

  // Related terms lists
  $output .= '<ul id="first-list-related" class="g-related-term"></ul>';
  $output .= '<ul id="second-list-related" class="g-related-term"></ul>';

  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';

  return $output;
}
add_shortcode('glossary_filter', 'glossary_filter_shortcode');
