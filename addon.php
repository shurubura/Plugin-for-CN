<?php
/*
Plugin Name: Addon for CN
Plugin URI: https://github.com/shurubura/
description: My plugin to add custom functionality for CN plugin
Version: 1.0
Author: Shurubura Kostiantyn
Author URI: https://t.me/kostyashurubura
License: GPL2
*/

/* 1. Display a warning message requesting to install the basic plugin */
add_action( 'admin_init', 'child_plugin_has_parent_plugin' );

function child_plugin_has_parent_plugin() {
   if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'codingninjasdev-cn_php_wp_plugin_for_tasks-77f07d486783/coding-ninjas.php' ) ) {
      add_action( 'admin_notices', 'child_plugin_notice' );

      deactivate_plugins( plugin_basename( __FILE__ ) ); 

      if ( isset( $_GET['activate'] ) ) {
         unset( $_GET['activate'] );
      }
   }
}

function child_plugin_notice(){?>
   <div class="notice notice-warning"><p>Sorry, but Addon for CN requires the Coding Ninjas Tasks plugin to be installed and active.</p></div>
<?php }

/* 2. Create a post type Freelancer*/

/*
* Creating a function to create our CPT
*/
 
function custom_post_type() {
 
// Set UI labels for Custom Post Type
   $labels = array(
      'name'                => 'Freelancers',
      'singular_name'       => 'Freelancer',
      'name_admin_bar'      => 'Freelancers',
      'all_items'           => 'All Freelancers',
      'view_item'           => 'View Freelancer',
      'new_item'            => 'New Freelancer',
      'add_new_item'        => 'Add New Freelancer',
      'edit_item'           => 'Edit Freelancer',
      'update_item'         => 'Update Freelancer',
      'search_items'        => 'Search Freelancer',
      'not_found'           => 'No Freelancers Found',
      'not_found_in_trash'  => 'Not Freelancers found in Trash',
   );
     
// Set other options for Custom Post Type
     
   $args = array(
      'label'               => 'Freelancers',
      'description'         => 'Freelancers for tasks',
      'labels'              => $labels,
      // Features this CPT supports in Post Editor
      'supports'            => array( 'title','thumbnail'),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'rewrite'            => array( 'slug' => 'freelancers' ),
      'menu_icon'            => 'dashicons-businessperson',
      'can_export'          => true,
      'has_archive'         => true,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => 'post',
      'show_in_rest' => true,
 
   );
     
    // Registering Custom Post Type
    register_post_type( 'freelancers', $args );
 
}
 
add_action( 'init', 'custom_post_type', 0 );


/* 3. Add a metabox with a drop-down list of all freelancers */
add_action( 'add_meta_boxes', 'add_freelancer_metabox' );
/*
* Adds a Freelancer metabox to the task post type
*/
function add_freelancer_metabox() {
   add_meta_box(
      'meta_add_freelancer',
      'Freelancer',
      'meta_add_freelancer',
      'task',
      'side',
      'default'
   );
}

/*
* Output for the metabox.
*/
function meta_add_freelancer() {
   // Nonce field to validate form request came from current site
   wp_nonce_field( basename( __FILE__ ), 'tasks_fields' );
   echo '<select id="freelancers" name="freelancers">
         <option value="">Select Freelancer</option>';

   global $post;
   $count_posts = wp_count_posts( 'freelancers' )->publish;
   $value = get_post_meta($post->ID, 'freelancer_key', true);

   if ($count_posts!=0) {
      $query = new WP_Query( [ 'post_type' => 'freelancers','post_status' => 'publish'] );

      while ( $query->have_posts() ) : $query->the_post();
         $post_id=get_the_ID();
         $post_title=get_the_title(); ?>
      <option value="<?php echo $post_id ?>" <?php echo selected($value, $post_id) ?>><?php echo $post_title?></option>
      <?php endwhile;

   wp_reset_postdata(); 

   }
   echo '</select>';
}

function save_freelancer_metabox($post_id){
   if (array_key_exists('freelancers', $_POST)) {
      update_post_meta(
         $post_id,
         'freelancer_key',
         $_POST['freelancers']
      );
   }
}

add_action('save_post', 'save_freelancer_metabox');

/* 4. Change page titles for the pages “Tasks” and “Dashboard” */

add_filter( 'pre_get_document_title', 'change_title' );

function change_title () {
   if ($_SERVER['REQUEST_URI']=="/dashboard") {
      return "Dashboard";
   } elseif ($_SERVER['REQUEST_URI']=="/tasks"){
      return "Tasks";
   }
}

/* 5. Add column "Freelancer" */

function add_fl_title () {
   $cols = [
            __('ID', 'cn'),
            __('Title', 'cn'),
            __('Freelancer','cn'),
            __('Date', 'cn')
        ];
   $cols = apply_filters ('cn_tasks_thead_cols', $cols);

}

add_filter( 'init', 'add_fl_title' );

add_filter( 'cn_tasks_thead_cols', 'add_new_column');

function add_new_column($cols) {
   $cols[3]=$cols[2];
   $cols[2]=__('Freelancer', 'cn');
   return $cols;
}

add_filter( 'cn_tasks_tbody_rows', 'change_cols');

function change_cols ($rows) {
   /*if ($posts) {
            foreach ($posts as $post) {
               $task_id=$posts[0];
               $fl_id=get_post_meta(ltrim($posts[0], $posts[0][0]))['freelancer_key'][0];
               $fl_name=["ID" => $task_id,
                           "Freelancer" => get_the_title($fl_id)];
            }
        }*/
        if ($rows) {
            foreach ($rows as &$row) {
              $row[3]=$row[2];
              $row[2]="";
            }
     }

        return $rows;
        //echo count($posts);
        //echo "Hello";
        //$cols = apply_filters('cn_tasks_tbody_row_cols', $cols, $task);
        
}

function script_that_requires_jquery() {
   wp_enqueue_script( 'cust-js', plugin_dir_url( __FILE__ ) . "/assets/js/custom.js", array( 'jquery' ),'1.0.0',true);

   wp_localize_script('cust-js', 'fileProp', array(
      'fileurl' => plugin_dir_url( __FILE__ )
   ));
}

add_action( 'wp_enqueue_scripts', 'script_that_requires_jquery' );


/* 7. Add new menu item */
add_filter( 'cn_menu', 'add_new_menu');

function add_new_menu($menu) {
 
   $menu['/add_new']=[
               'title' => __('Add New Task', 'cn'),
               'icon' => 'fa-plus-circle'
            ];
 
   return $menu;
}


add_filter( 'wp_enqueue_scripts', 'add_new_btn' );

function add_new_btn() {
if (($_SERVER['REQUEST_URI']=="/tasks")) {
   include "popup.php";
   echo $modal;
   }
}

function add_ajax(){

//file where AJAX code will be found 
wp_enqueue_script( 'my-ajax', plugin_dir_url( __FILE__ ) . "/assets/js/ajax.js", array('jquery'), true );

//passing variables to the javascript file
wp_localize_script('my-ajax', 'frontEndAjax', array(
 'ajaxurl' => admin_url( 'admin-ajax.php' ),
 'nonce' => wp_create_nonce('ajax_nonce')
 ));

}
add_action( 'wp_enqueue_scripts', 'add_ajax' );



function task_addpost() {
    $results = '';
 
    $title = $_POST['title_task'];
    $flname =  $_POST['freelancers'];
 if ($title!='') {
    $post_id = wp_insert_post( array(
        'post_type'    => 'task',
        'post_title'    => $title,
        'freelancer_key'  => $flname,
        'post_status'   => 'publish',
        'post_author'   => $user_id,
    ) );
}
}

// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv__addpost', 'task_addpost' );
add_action( 'wp_ajax_task_addpost', 'task_addpost' );

/* 8.Add dashboard */

function wpb_demo_shortcode() { 
   $params =[
      ["type"=>"freelancers",
       "color"=>"blue",
       "icon"=>"fa-users",
       "name"=>"Freelancers"],
      ["type"=>"task",
       "color"=>"green",
       "icon"=>"fa-tasks",
       "name"=>"Tasks"
      ]
   ];

foreach ($params as $key) {
 $args = array(
                    'post_type'      => $key['type'],
                    'publish_status' => 'published',
                 );
 $query = new WP_Query($args);
 if($query->have_posts()) :
   $count = $query->found_posts;
   $result .= '<div class="col-lg-3">';
        $result .= '<div class="dashboard-post ' . $key['color'] . '"><i class="fa ' . $key['icon'] . '"></i><div class="dash-spans"><span class="count">' . $count . '</span><span>'. $key['name'] .'</span></div></div>';
        $result .= '</div>';
   wp_reset_postdata();
endif;   
}
return $result;
} 
// register shortcode
add_shortcode('cn_dashboard', 'wpb_demo_shortcode'); 














?>