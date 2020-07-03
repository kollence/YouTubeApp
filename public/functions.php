<?php
function my_podcasts() {
    $args= [
		'numberposts' => 10,
		'post_type' => 'podcasts'
	];

	$podcasts = get_posts($args);
	$data = [];
	$i = 0;

	foreach($podcasts as $podcast) {
		$data[$i]['id'] = $podcast->ID;
		$data[$i]['title'] = $podcast->post_title;
		$data[$i]['content'] = $podcast->post_content;
		$data[$i]['date'] = get_the_date('', $podcast->ID);
		$data[$i]['cards'] = $podcast->cards;
		$i++;
	}
	return $data;
	// return $podcasts;
}


add_action('rest_api_init', function() {
    register_rest_route('my-api/v1', 'podcasts', array(
        'methods' => 'GET',
        'callback' => 'my_podcasts',
	));
});

// function my_custom_fields(){
// 	register_rest_field('podcasts','videoWithCards', array(
// 		'get_callback' => function() {
// 			// $args= [
// 			// 	'numberposts' => 10,
// 			// 	'post_type' => 'podcasts'
// 			// ];
		
// 			// $podcasts = get_posts($args);
// 			// $data = [];
// 			// $i = 0;
		
// 			// foreach($podcasts as $podcast) {
// 			// 	$data[$i]['id'] = $podcast->ID;
// 			// 	$data[$i]['title'] = $podcast->post_title;
// 			// 	$data[$i]['content'] = $podcast->post_content;
// 			// 	$data[$i]['myarray'] = array();
// 			// 	$i++;
// 			// }
// 			$data = array();
// 			return $data;
// 		}
// 	));
// };

// add_action('rest_api_init', 'my_custom_fields');

add_action('rest_api_init', 'my_custom_endpoints');

function my_custom_endpoints() {
	// Handle GET request
	// on http://localhost/worpress/wp-json/my-custom/v1/podcasts?page_no=1
	register_rest_route('my-custom/v1', 'podcasts', [
		'method' => 'POST',
		'callback' => 'custom_endpoint_handler'
	]);
}
function custom_endpoint_handler(WP_REST_Request $request) {
	$response = [];
	$parameters = $request->get_params();
	// number of post on 
	$post_page_no = ! empty($parameters['page_no']) ? intval(sanitize_text_field($parameters['page_no'])) : '';
	$error = new WP_Error();
	$posts_data = get_my_posts($post_page_no);

	if(! empty($posts_data['podcasts'])) {
		$response['status'] = 200;
		$response['podcasts'] = $posts_data['podcasts'];
		$response['total_podcasts'] = $posts_data['total_podcasts'];
		$response['page_count'] = $posts_data['page_count'];

	}else {
		$error->add(406, __('Posts not found', 'rest-api-endpoints'));
		return $error;
	}
	// return $posts_data;
	return new WP_REST_Response($posts_data);

}

function calculate_page_count($total_found, $post_per_page) {
	return (int) ($total_found / $post_per_page) + (($total_found % $post_per_page) ? 1 : 0);
}

function get_my_posts($page_no = 1){
	$post_pp = 6;
	$args = [
		'post_type' => 'podcasts',
		'post_status' => 'publish',
		'posts_per_page' => $post_pp,
		// 'fields' => 'ids',
		'orderby' => 'date',
		'paged' => $page_no,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	];
	
	$podcasts = get_posts($args);
	$data = [];
	$i = 0;
	foreach($podcasts as $podcast) {
		// $data[$i]['date'] = get_the_date('', $podcast->ID);
		$data[$i]['id'] = $podcast->ID;
		$data[$i]['title'] = $podcast->post_title;
		$data[$i]['content'] = $podcast->post_content;
		$data[$i]['date'] = get_the_date('', $podcast->ID);
		$data[$i]['cards'] = $podcast->cards;
		$i++;
	}

	$found = new WP_Query($args);
	$found_potcasts = $found->found_posts;
    $page_count = calculate_page_count($found_potcasts, $post_pp);
	return [
		'podcasts' => $data,
		'total_podcasts' => $found_potcasts,
		'page_count' => $page_count
	];
}