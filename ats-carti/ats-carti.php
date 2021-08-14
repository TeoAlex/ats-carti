<?php 
/**
 * Plugin Name: AlTeSin Modul Carti
 * Plugin URI: #
 * Description: Modul in care un admin din wordpress poate adauga carti.
 * Version: 1.0
 * Author: Alex Teo
 * Author URI: #
 */


function ats_tax_reg() {


	// Inregistrez - Taxonomy: Genuri.


	$labels = [
		"name" => __( "Genuri", "text_domain" ),
		"singular_name" => __( "Gen", "text_domain" ),
	];

	
	$args = [
		"label" => __( "Genuri", "text_domain" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'gen', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "gen",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "gen", [ "post_type_carte" ], $args );
}
add_action( 'init', 'ats_tax_reg' );


function ats_carti_reg() {



	// Inregistrez - Post Type: Carti.

	$labels = [
		"name" => __( "Carti", "text_domain" ),
		"singular_name" => __( "Carte", "text_domain" ),
		"menu_name" => __( "Carti", "text_domain" ),
		"all_items" => __( "Toate Cartile", "text_domain" ),
		"add_new" => __( "Adauga Carte", "text_domain" ),
		"add_new_item" => __( "Adauga Carte", "text_domain" ),
		"edit_item" => __( "Editeaza Carte", "text_domain" ),
		"new_item" => __( "Carte Noua", "text_domain" ),
		"view_item" => __( "Vezi Carte", "text_domain" ),
		"view_items" => __( "Vezi Carti", "text_domain" ),
		"search_items" => __( "Cauta Carti", "text_domain" ),
		"not_found" => __( "Nu s-au gasit Carti", "text_domain" ),
		"not_found_in_trash" => __( "Nu s-au gasit Carti in gunoi", "text_domain" ),
		"parent" => __( "Parinte Carte:", "text_domain" ),
		"featured_image" => __( "Imagine reprezentativa pentru Carte", "text_domain" ),
		"set_featured_image" => __( "Foloseste imaginea pentru Carte", "text_domain" ),
		"remove_featured_image" => __( "Inlatura imaginea pentru Carte", "text_domain" ),
		"use_featured_image" => __( "Foloseste aceasta imagine pentru Carte", "text_domain" ),
		"name_admin_bar" => __( "Carte", "text_domain" ),
		"item_published" => __( "Cartea a fost publicata", "text_domain" ),
		"item_published_privately" => __( "Cartea a fost publicata ca privata.", "text_domain" ),
		"item_reverted_to_draft" => __( "Cartea a fost publicata ca ciorna.", "text_domain" ),
		"item_scheduled" => __( "Carte programata", "text_domain" ),
		"item_updated" => __( "Carte actualizata.", "text_domain" ),
		"parent_item_colon" => __( "Parinte Carte:", "text_domain" ),
	];

	$args = [
		"label" => __( "Carti", "text_domain" ),
		"labels" => $labels,
		"description" => "",
		"autor" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "carti", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "gen" ],
		"show_in_graphql" => false,
	];

	register_post_type( "carti", $args );
}

add_action( 'init', 'ats_carti_reg' );

/* ----- START META BOX AUTOR ------*/

// functie de adaugare meta box pentru nume autor
function add_mb_author() {

  $screens = array( 'carti' );

  foreach ( $screens as $screen ) {


    add_meta_box('input_autor_carte', __( 'Autor Carte', 'text_domain' ), 'fill_mb_content', $screen);

  }
}


add_action( 'add_meta_boxes', 'add_mb_author' );



// functie de adaugare continut in meta box-ul "Nume Autor"
function fill_mb_content( $post, $box ) {

  wp_nonce_field( 'save_mb_value', 'save_mb_value_nonce' );
  $autor_carte = get_post_meta( $post->ID, '_autor_carte', true );

  if ($autor_carte) {
    $autor_carte = json_decode($autor_carte, true);
  }

  ?>
        <input 
          name="nume_autor" 
          type="text" 
          class="components-text-control__input" 
          style="min-width: 200px;"
          value="<?php echo isset($autor_carte) ? $autor_carte : '' ?>">
  <?php
wp_reset_postdata();

}

//functie verificare nonce,autosave,form validation si pentru save
function save_mb_value($post_id, $post)
{

    if (!isset($_POST['save_mb_value_nonce']))
    {
        return;
    }

    if (!wp_verify_nonce($_POST['save_mb_value_nonce'], 'save_mb_value'))
    {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    {
        return;
    }

    if (!isset($_POST['nume_autor']))
    {
        return;
    }

    // curat input (nu stiu daca se merita cu json)
    $nume_autor = json_encode($_POST['nume_autor']);

    // insert in BD
    update_post_meta($post_id, '_autor_carte', $nume_autor);

}

add_action('save_post', 'save_mb_value', 10, 2);

/* ----- END META BOX AUTOR ------*/

//Enqueue stil
function ats_load_plugin_css()
{

    wp_enqueue_style('style1', get_option('siteurl') . '/wp-content/plugins/ats-carti/css/style.css');
    wp_enqueue_style('style2', get_option('siteurl') . '/wp-content/plugins/ats-carti/css/bootstrap/bootstrap.min.css');
}
add_action('wp_enqueue_scripts', 'ats_load_plugin_css');

//Enqueue javascript
function ats_enqueue_scripts()
{

    wp_enqueue_script('dfields1', get_option('siteurl') . '/wp-content/plugins/ats-carti/js/bootstrap/bootstrap.min.js', array(
        'jquery'
    ) , '1112222', true);
    wp_enqueue_script('dfields2', get_option('siteurl') . '/wp-content/plugins/ats-carti/js/js.js', array(
        'jquery'
    ) , '1112222', true);
}
add_action('wp_enqueue_scripts', 'ats_enqueue_scripts');

//Functie de query meta value (aici: Nume Autor) care apartine post_type ales (default: 'carti')
function get_meta_values($meta_key, $post_type = 'carti')
{

    $posts = get_posts(array(
        'post_type' => $post_type,
        'meta_key' => $meta_key,
        'posts_per_page' => - 1,
    ));

    $meta_values = array();
    foreach ($posts as $post)
    {
        $meta_values[] = get_post_meta($post->ID, $meta_key, true);
    }

    return $meta_values;

}

/* ----- START SHORTCODE ------*/

//functie creare shortcode
function ats_make_shortcode($attr)
{

    $result = $options_gen = $options_autor = '';

    $result .= '
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-12 my-3 ">
					<div class="pull-left">
						<div class="btn-group">
							<button class="btn btn-info" id="lista">
								Lista
							</button>
							<button class="btn btn-info active" id="grila">
								Grila
							</button>
						</div>
					</div>
				</div>';

    if (isset($attr['gen']) and $attr['gen'] != '')
    {

        $result .= '
			</div> 
			<div id="carti" class="row row-eq-height view-group">';

        $args = array(
            'post_type' => 'carti',
            'posts_per_page' => 10,
            'tax_query' => array(
                array(
                    'taxonomy' => 'gen',
                    'field' => 'name',
                    'terms' => $attr['gen'],
                )
            )
        );
    }
    else
    {
        $terms = get_terms(['taxonomy' => 'gen', 'hide_empty' => false, ]);
        foreach ($terms as $term)
        {
            $options_gen .= '<option value="' . $term->name . '">' . $term->name . '</option>';
        }

        $authors = array_unique(get_meta_values('_autor_carte'));
        foreach ($authors as $author)
        {
            $options_autor .= '<option value="' . json_decode($author) . '">' . json_decode($author) . '</option>';
        }
        $result .= '
				<div class="col-lg-4 col-6 my-3 px-1">
					<div class="input-group mb-3">
					  <select class="custom-select" id="gen_select">
						<option value="toate" selected="">Toate Genurile</option>
							' . $options_gen . ' 
					  </select>
					</div>
				</div>
				<div class="col-lg-4 col-6 my-3 px-1">
					<div class="input-group mb-3">
					  <select class="custom-select" id="author_select">
						<option value="toate" selected="">Toti Autorii</option>
							' . $options_autor . ' 
					  </select>
					</div>
				</div>
			</div> 
			<div id="carti" class="row row-eq-height view-group">';

        $args = array(
            'post_type' => 'carti',
            'posts_per_page' => 10
        );
    }

    $loop = new WP_Query($args);
    while ($loop->have_posts()):
        $loop->the_post();

        //Convertesc tax din array in string
        $tax_arr = get_the_terms(get_the_ID() , 'gen');
        $tax_str = join(',', wp_list_pluck($tax_arr, 'name'));

        //Lungime max string
        $description = get_the_content(get_the_ID());
        $description = strlen($description) > 50 ? substr($description, 0, 250) . "..." : $description;

        $result .= '
					<div class="ats-book-container item col-xs-3 col-lg-3 grid-group-item" data-gender = "' . $tax_str . '" data-author = "' . json_decode(get_post_meta(get_the_ID() , '_autor_carte', true)) . '">
						<div class="thumbnail card">
							<div class="img-event">
								<img class="group list-group-image img-fluid ats-book-image" src="' . get_the_post_thumbnail_url(get_the_ID() , 'full') . '" alt="" />
							</div>
							<div class="caption card-body">
							    <div class="ats-book-ta">
    								<h4 class="group inner list-group-item-heading ats-book-title">
    								' . get_the_title(get_the_ID()) . '	
    								</h4>
    								<p class="group inner list-group-item-text ats-book-author">
    								' . json_decode(get_post_meta(get_the_ID() , '_autor_carte', true)) . '
    								</p>
								</div>
								<p class="group inner list-group-item-text ats-book-gender">
								' . $tax_str . '
								</p>
								<p class="group inner list-group-item-text ats-book-description">
								' . $description . '	
								</p>
							</div>
						</div>
					</div>
				
				';

    endwhile;
    if ($loop->have_posts())
    {
        $empty = 'style="display:none;"';
    }
    else
    {
        $empty = 'style="display:block;"';
    }
    $result .= '
				<div id="no_books" class="ats-book-container-empty item col-xs-12 col-lg-12" ' . $empty . '>
					<div class="thumbnail card">
						<div class="caption card-body">
							<p class="group inner list-group-item-text ats-book-empty">
							    Nu s-au gasit carti.
							</p>
						</div>
					</div>
				</div>';
    $result .= '
					
				</div>
			</div>
			';

    return $result;
}

// adaug shortcod
add_shortcode('ats_books', 'ats_make_shortcode');

/* ----- END SHORTCODE ------*/

// disable Gutemberg pentru Post Carti
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'carti') return false;
    return $current_status;
}

