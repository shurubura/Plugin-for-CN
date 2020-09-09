<?php 
$modal='<div class="modal fade" style="display:none" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add New Task</h4>
        <h2 id="resp"></h2>
      </div>
      <form method="POST" class="tasks-form" action="">
      <div class="modal-body">
      <div class="task-title"><span class="title-creation">Task title</span><input id="task_name" name="title_task" type="text"></div>
      <div class="freelancer-selection"><span class="freelancer-choose">Freelancer</span>';
   $modal.='<select id="freelancers" name="freelancers">
            <option value="">Select Freelancer</option>';

   global $post;
   $count_posts = wp_count_posts( 'freelancers' )->publish;
   $value = get_post_meta($post->ID, 'freelancer_key', true);

   if ($count_posts!=0) {
      $query = new WP_Query( [ 'post_type' => 'freelancers','post_status' => 'publish'] );

      while ( $query->have_posts() ) : $query->the_post();
         $post_id=get_the_ID();
         $post_title=get_the_title(); 
      $modal.='<option value="'. $post_id .'"'. selected($value, $post_id) .'>' . $post_title . '</option>';
      endwhile;

   wp_reset_postdata(); 

   }
   $modal.='</select>';

    $modal.='</div>
      </div>
      <div class="add_btn"><button type="submit" onclick="addtask(task_name.value,freelancers.value);" class="btn btn-primary">Add</button></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </form>
    </div>
  </div>
</div>';

return $modal;