<?php
function pop_supported_by_choices($field)
{
	$args = array(
		'post_type' => 'avada_portfolio',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	);

	$field['choices'] = array();
	$field['choices']['--SELECT--'] = '--SELECT--';

	$portfolio_query = new WP_Query($args);
	if ($portfolio_query->have_posts()) {
		while ($portfolio_query->have_posts()) {
			$portfolio_query->the_post();
			$label = $value = get_the_title();

			$field['choices'][$value] = $label;
		}
		wp_reset_postdata();
	} else {
		$field['choices'][$value] = "NO TITLES";
	}

	return $field;
}
add_filter('acf/load_field/name=supported_by', 'pop_supported_by_choices');
