function addtask(posttitle,name) {
 if (posttitle!='') {
    jQuery.ajax({
 
        type: 'POST',
 
        url: frontEndAjax.ajaxurl,
 
        data: {
            action: 'task_addpost',
            title_task: posttitle,
            freelancers: name
        },
 
        success: function(data, textStatus, XMLHttpRequest) {
            alert("Success");
 
            resetvalues();
		    location.reload(true);
        },
 
        error: function(MLHttpRequest, textStatus, errorThrown) {

            alert(errorThrown);
        }
 
    });
}
}
 
function resetvalues() {
 
    var title = document.getElementById("task_name");
    title.value = '';
 
    var content = document.getElementById("freelancers");
    content.value = '';
 
}