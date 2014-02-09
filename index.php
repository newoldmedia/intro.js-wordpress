<?php

/*
  Plugin Name: Intro.js Tours for WordPress
  Plugin URI: https://github.com/newoldmedia/intro.js-wordpress
  Description: Create Product Tours with Intro.js.
  Author: Rakhitha Nimesh for New Old Media
  Version: 1.1
  Author URI: http://newoldmedia.net
 */


class WP_Introjs{
	public $post_type;

    public $error;

	public function __construct(){
		$this->post_type = 'wp_intro_tours';

		add_action('init', array($this,'register_product_tours'));

		add_action('admin_menu', array($this,'create_menu_pages'));

		add_action('wp_enqueue_scripts', array($this,'enqueue_introjs_script'));

		//add_action('init', array($this,'save_product_steps'));

		add_action('admin_enqueue_scripts', array($this,'admin_scripts'));

		add_action('wp_ajax_nopriv_wpintro_update_step_order', array($this,'update_step_order'));

		add_action('wp_ajax_wpintro_update_step_order', array($this,'update_step_order'));

        add_filter( 'manage_edit-wp_intro_tours_columns', array($this,'edit_wp_intro_tours_columns') );

        add_action( 'manage_wp_intro_tours_posts_custom_column', array($this,'manage_wp_intro_tours_columns'), 10, 2 );
        add_filter( 'manage_edit-wp_intro_tours_sortable_columns', array($this,'wp_intro_tours_sortable_columns') );

        add_shortcode('intro_tour' , array($this,'display_product_tour'));
	}

	public function register_product_tours(){
		$labels = array(
            'name'                  => __( 'Product Tours', 'wpintrojs' ),
            'singular_name'         => __( 'Product Tour', 'wpintrojs' ),
            'add_new'               => __( 'Add New', 'wpintrojs' ),
            'add_new_item'          => __( 'Add New Product Tour', 'wpintrojs' ),
            'edit_item'             => __( 'Edit Product Tour', 'wpintrojs' ),
            'new_item'              => __( 'New Product Tour', 'wpintrojs' ),
            'all_items'             => __( 'All Product Tours', 'wpintrojs' ),
            'view_item'             => __( 'View Product Tour', 'wpintrojs' ),
            'search_items'          => __( 'Search Product Tours', 'wpintrojs' ),
            'not_found'             => __( 'No Product Tours found', 'wpintrojs' ),
            'not_found_in_trash'    => __( 'No Product Tours found in the Trash', 'wpintrojs' ),
            'parent_item_colon'     => '',
            'menu_name'             => __('Product Tours', 'wpintrojs' ),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'description'           => 'Product Tours',
            'supports'              => array('title', 'editor'),
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'publicly_queryable'    => true,
            'exclude_from_search'   => false,
            'has_archive'           => true,
            'query_var'             => true,
            'can_export'            => true,
            'rewrite'               => true,
            'capability_type'       => 'post',

        );

        register_post_type( $this->post_type, $args );
	}

	public function create_menu_pages() {
    add_menu_page('Introjs Tours', 'Introjs Tours', 'manage_options', 'wpintro_tour', array($this,'menu_page_display'));
    add_submenu_page('wpintro_tour', 'Manage Steps', 'Manage Steps', 'manage_options', 'wpintro_tour_steps',array($this,'manage_steps_display'));
}

public function menu_page_display() {
    global $wip_message;

    $pages = get_pages();

    $html = '<div class="wpintro_main">';
    

    $error = '';
    $wpi_tour = '';
    $wpi_page = '';
    $wpi_tooltip_pos = '';
    $wpi_intro_text = '';
    $wpi_element_id = '';
    $wpi_tooltip_class = '';

    if($_POST){

        $wpi_tour = isset($_POST['wpi_tour']) ? $_POST['wpi_tour'] : '';
        $wpi_page = isset($_POST['wpi_page']) ? $_POST['wpi_page'] : '';
        $wpi_tooltip_pos = isset($_POST['wpi_tooltip_pos']) ? $_POST['wpi_tooltip_pos'] : '';
        $wpi_intro_text = isset($_POST['wpi_intro_text']) ? $_POST['wpi_intro_text'] : '';
        $wpi_element_id = isset($_POST['wpi_element_id']) ? $_POST['wpi_element_id'] : '';
        $wpi_tooltip_class = isset($_POST['wpi_tooltip_class']) ? $_POST['wpi_tooltip_class'] : '';

        if('0' == $wpi_tour){
            $this->error .= '<p>'. __('Product Tour is required.','wpintrojs'). '</p>';
        }
        if('0' == $wpi_page){
            $this->error .= '<p>'. __('Tour Page is required.','wpintrojs'). '</p>';
        }
        if('' == $wpi_intro_text){
            $this->error .= '<p>'. __('Intro Text is required.','wpintrojs'). '</p>';
        }
        if('' == $wpi_element_id){
            $this->error .= '<p>'. __('Element ID is required.','wpintrojs'). '</p>';
        }

        if('' == $this->error){
            $this->save_product_steps();
        }
    }

    if ($wip_message != '') {
        $html .= '<div style="background:#9FD09F;padding:5px;">' . $wip_message . '</div>';
    }

    if('' != $this->error){
        $html .= '<div class="wpintro_errors">' . $this->error . '</div>';
    }

    $html .= '<h2>'. __('Create Product Tour','wpintrojs').'</h2>';

    $html .= '<form action="" method="post">';

    $html .= '<table class="form-table"><tbody>';
    $html .= '	<tr valign="top">
			<th scope="row"><label>' .__('Product Tour','wpintrojs').'</label></th>
			<td><select class="regular-text" id="wpi_tour" name="wpi_tour">
    			<option value="0" '. selected( $wpi_tour , '0' ,false) .'  >Select Tour</option>';
            $args = array(                
                'post_type' => $this->post_type,
                'order'             => 'DESC',
                'orderby'           => 'date',
                'posts_per_page'    => '-1',
		'post_status'=>'publish'
            );

            $query = new WP_Query( $args );

            if($query->have_posts()){
        		while ($query->have_posts()) : $query->the_post();
        			$html .= '<option '. selected( $wpi_tour , get_the_ID() ,false ) .'  value="' . get_the_ID() . '">';
                		$html .= get_the_title();
                		$html .= '</option>';
                        endwhile;

                        wp_reset_query();

        	    }



    $html .= '		</select></td>
		</tr>';
    $html .= '	<tr valign="top">
			<th scope="row"><label>' .__('Tour Page','wpintrojs').'</label></th>
			<td><select class="regular-text" id="wpi_page" name="wpi_page">
                <option value="0"  '. selected( $wpi_page , '0',false ) .' >Select Page</option>
		<option value="other"  '. selected( $wpi_page , 'other',false ) .' >Other</option>';
    			
			foreach ($pages as $page) {
        			$html .= '<option  '. selected( $wpi_page , get_page_link($page->ID) ,false) .' value="' . get_page_link($page->ID) . '">';
        			$html .= $page->post_title;
        			$html .= '</option>';
    			}


    $html .= '		</select></td>
		</tr>';

    $html .= '  <tr id="wpi_custom_url_panel" valign="top" style="display:none" >
            		<th scope="row"><label>' . __('Custom URL','wpintrojs') . '</label></th>
			<td><input type="text" class="regular-text"  value="" id="wpi_custom_url" name="wpi_custom_url"></td>
        	</tr>';

    $html .= '  <tr valign="top">
            <th scope="row"><label>' . __('Tooltip Position','wpintrojs') . '</label></th>
            <td>
                <select class="regular-text" id="wpi_tooltip_pos" name="wpi_tooltip_pos">
                    <option value="bottom" '. selected( $wpi_tooltip_pos , 'bottom',false ) .' >Bottom</option>
                    <option value="top" '. selected( $wpi_tooltip_pos , 'top',false ) .' >Top</option>
                    <option value="left" '. selected( $wpi_tooltip_pos , 'left',false ) .' >Left</option>
                    <option value="right" '. selected( $wpi_tooltip_pos , 'right',false ) .'>Right</option>                    
                </select>
            </td>
        </tr>';

    $html .= '	<tr valign="top">
			<th scope="row"><label>' . __('Intro Text','wpintrojs') . '</label></th>
			<td><textarea class="regular-text" id="wpi_intro_text" name="wpi_intro_text">'.$wpi_intro_text.'</textarea></td>
		</tr>';
    $html .= '	<tr valign="top">
			<th scope="row"><label>' . __('Element ID','wpintrojs') . '</label></th>
			<td><input type="text" class="regular-text"  value="'.$wpi_element_id.'" id="wpi_element_id" name="wpi_element_id"></td>
		</tr>';


    $html .= '  <tr valign="top">
            <th scope="row"><label>' . __('Tooltip Class','wpintrojs') . '</label></th>
            <td><input type="text" class="regular-text" value="'.$wpi_tooltip_class.'"  id="wpi_tooltip_class" name="wpi_tooltip_class"></td>
        </tr>';

    $html .= '	<tr valign="top">
			<th scope="row"><label></label></th>
			<td><input type="hidden" class="regular-text"  id="wpi_action" name="wpi_action" value="save_steps" >
			<input type="submit" class="regular-text"  id="wpi_submit" name="wpi_submit" value="Save" ></td>
		</tr>';
    $html .= '</form></div>';
    echo $html;
}

public function manage_steps_display() {

    $wpi_tour = '';
    if($_POST){
        $wpi_tour = isset($_POST['wpi_tour']) ? $_POST['wpi_tour'] : '';
    }

    $html  = '<form action="" method="POST" >';
    $html .= '<table class="form-table"><tbody>';
    

    $html .= '  <tr valign="top">
            <th scope="row"><label>' .__('Product Tour','wpintrojs').'</label></th>
            <td><select class="regular-text" id="wpi_tour" name="wpi_tour">
                <option value="0" '. selected( $wpi_tour , '0' ,false) .'  >Select Tour</option>';
            $args = array(                
                'post_type' => $this->post_type,
                'order'             => 'DESC',
                'orderby'           => 'date',
                'posts_per_page'    => '-1',
        'post_status'=>'publish'
            );

            $query = new WP_Query( $args );

            if($query->have_posts()){
                while ($query->have_posts()) : $query->the_post();
                    $html .= '<option '. selected( $wpi_tour , get_the_ID() ,false ) .'  value="' . get_the_ID() . '">';
                        $html .= get_the_title();
                        $html .= '</option>';
                        endwhile;

                        wp_reset_query();

                }

    $html .= '</select></td>
                </tr>';
    $html .= '  <tr valign="top">
            <th scope="row"><label></label></th>
            <td><input type="hidden" class="regular-text"  id="wpi_action" name="wpi_action" value="manage_steps" >
            <input type="submit" class="regular-text"  id="wpi_submit" name="wpi_submit" value="Submit" ></td>
        </tr>';


    $html .= '</tbody></table>';
    $html .= '</form>';

    

    $step_data = get_post_meta($wpi_tour ,'wpi_step_data', true);
    if ($step_data != '') {
        $step_data = unserialize($step_data);
    }
    $html .= "<div id='sortable'>";

    if(is_array($step_data)){
        foreach ($step_data as $key => $data) {
            $html .= "<div class='wip_sort' style='margin:5px;
      border:2px solid;
      background: #fff;' data-identifier=" . $key . "  >
    		<ul>
    			<li><strong>URL : </strong>" . $data['url'] . "</li>
    			<li ><strong>Description : </strong>" . $data['desc'] . "</li>
    			<li><strong>Element ID : </strong>" . $data['id'] . "</li>
    		</ul>
		<div class='wpi_del_panel'><input type='button' class='wpi_del' value='Delete' /></div>
                <div style='clear:both'></div>
    	      </div>";
        }
    }
    $html .= "</div>";

    echo $html;
}

public function enqueue_introjs_script() {

    wp_enqueue_script('jquery');

    wp_register_script('introjs', plugin_dir_url(__FILE__) . 'intro.js');
    wp_enqueue_script('introjs');

    wp_register_style('introjscss', plugin_dir_url(__FILE__) . 'introjs.css');
    wp_enqueue_style('introjscss');


    if(isset($_GET['tour_id'])){
	$this->assign_step_data_to_scripts($_GET['tour_id']);
    }
    
}

public function save_product_steps() {
    
    global $wip_message;
    if (isset($_POST['wpi_action']) && $_POST['wpi_action'] == 'save_steps') {
        

        $tour_id =  $_POST['wpi_tour']; 
        $intro_text = $_POST['wpi_intro_text'];
        $page = $_POST['wpi_page'];
        $element_id = $_POST['wpi_element_id'];
        $tooltip_class = $_POST['wpi_tooltip_class'];
        $position = $_POST['wpi_tooltip_pos'];


	if('other' == $page){
		$page = $_POST['wpi_custom_url'];
	}

        $step_data = get_post_meta( $tour_id, 'wpi_step_data', true );
        //$step_data = get_option('wpi_step_data', '');
        if ($step_data != '') {
            $step_data = unserialize($step_data);
            $step_data["wip" . rand(1000, 1000000)] = array("position" => $position, "tooltip_class" => $tooltip_class , "desc" => $intro_text, "url" => $page, "id" => $element_id);
        } else {
            $step_data = array("wip" . rand(1000, 1000000) => array("position" => $position, "tooltip_class" => $tooltip_class, "desc" => $intro_text, "url" => $page, "id" => $element_id));
        }

        $step_data = serialize($step_data);
        update_post_meta( $tour_id , 'wpi_step_data', $step_data);

        $wip_message = __('Step saved successfully','wpintrojs');
    } else {
        $wip_message = "";
    }

    return $wip_message;
}

function admin_scripts() {
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    wp_register_style('intro_admin_css', plugin_dir_url(__FILE__) . 'admin.css');
    wp_enqueue_style('intro_admin_css');

    wp_register_script('wpintro_admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'));
    wp_enqueue_script('wpintro_admin');

    $config_array = array(
        'ajaxURL' => admin_url('admin-ajax.php'),
    );

    wp_localize_script('wpintro_admin', 'conf', $config_array);
}



function update_step_order() {

    $tour_id = isset( $_POST['tour_id']) ?  $_POST['tour_id'] : '0';

    $step_data = get_post_meta($tour_id, 'wpi_step_data', true);
    $step_data = unserialize($step_data);

    $updates_step_order = array();
    $step_order = explode('@', $_POST['selected_id']);
    array_pop($step_order);
    for ($i = 0; $i < count($step_order); $i++) {
        $updates_step_order[$step_order[$i]] = $step_data[$step_order[$i]];
    }

    $step_data = serialize($updates_step_order);
    update_post_meta($tour_id, 'wpi_step_data', $step_data);

    echo json_encode($updates_step_order);
    exit;
}



function edit_wp_intro_tours_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Product Tour Name' ),
        'shortcode' => __( 'Product Tour Shortcode' ),
        'date' => __( 'Date' )
    );

    return $columns;
}


function manage_wp_intro_tours_columns( $column, $post_id ) {
    global $post;

    switch ( $column ) {

        case 'shortcode' :

            $tour_id = $post_id;

            if ( empty( $tour_id ) )
                echo __( 'Unknown' );

            else
                printf( '[intro_tour id='.$tour_id.' ]' );

            break;

        default :
            break;
    }
}



function wp_intro_tours_sortable_columns( $columns ) {

    $columns['shortcode'] = 'Shortcode';

    return $columns;
}

function display_product_tour($atts){

    extract( shortcode_atts( array(
        'id' => '',
    ), $atts ) );

    $this->assign_step_data_to_scripts($id);
}

function assign_step_data_to_scripts($id){
wp_register_script('wpintro_custom', plugin_dir_url(__FILE__) . 'custom.js', array('jquery'));
    wp_enqueue_script('wpintro_custom');

    $step_data = get_post_meta($id,'wpi_step_data', true);
    $step_data = unserialize($step_data);
    $step_data = array_values($step_data);
    $stepdata_array = array(
        'steps' => json_encode($step_data),
        'tour_id' => $id
    );

    wp_localize_script('wpintro_custom', 'stepData', $stepdata_array);
}

}

$introjs = new WP_Introjs();

$wip_message = '';
















