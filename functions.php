<?php
/**
 * Add a load more button to your page
 * @param array $args
 * @param int $paged. (optional) WP query var.
 * @return void
 */
function ez_load_more_button($args, $paged = 0) {
	if(!isset($args['template']) || !isset($args['label']) || !isset($args['context'])) {
		echo '<span style="color:red">Error - You should include the template, label and context variables in your $args array!</span>';
		return;
	}
	else {
		if (empty($paged)) {
			$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; 
		}
		
		$load_more = new EzLoadMore();
		$load_more->ez_load_more_button($args, $paged);
	}
}