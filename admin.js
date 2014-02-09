jQuery(document).ready(function(){

    jQuery( "#sortable" ).sortable({
        update: function() {
		wpiUpdateSteps();
            
        }

    });
    jQuery( "#sortable" ).disableSelection();

    jQuery( ".wpi_del" ).click(function(){
	jQuery(this).parent().parent().remove();
	wpiUpdateSteps();
    });

    jQuery( "#wpi_page" ).change(function(){
	if(jQuery(this).val() == 'other'){
		jQuery( "#wpi_custom_url_panel" ).show();
	}else{
		jQuery( "#wpi_custom_url_panel" ).hide();
	}	
    });
});

function wpiUpdateSteps(){
   var selected_ids = '';
            jQuery(".wip_sort").each(function() {
                selected_ids += jQuery(this).attr("data-identifier")+"@";
            });
            
            var tour_id = jQuery('#wpi_tour').val();

            jQuery.post(conf.ajaxURL, { 
                action: "wpintro_update_step_order", 
                selected_id:selected_ids,
                tour_id : tour_id
 
            }, function(data) { }, "json" );
}
