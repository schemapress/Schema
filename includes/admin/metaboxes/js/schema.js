jQuery(function($) {
	
	// Reset
	//$("#schema_article").hide();
	
	var schema_type = $("#_schema_type").val();
	
	if ( schema_type == 'Article')
		$("#schema_article").show();
	 
	$('#_schema_type').on('change', function() {
      if ( this.value == 'Article')
      //.....................^.......
      {
        $("#schema_article").show();
      }
      else
      {
        $("#schema_article").hide();
      }
    });
	
	
	// repeated post meta group
	$('#schema_post_meta_box').hide();
	
	var post_meta_enabled = $("#_schema_post_meta_box_enabled").attr('checked');
	
	if (post_meta_enabled)
		$('#schema_post_meta_box').show();
		
	$('#_schema_post_meta_box_enabled').change(function(){
        var checked = $(this).attr('checked');
        if (checked) {
           $('#schema_post_meta_box').show();             
        } else {
            $('#schema_post_meta_box').hide();
        }
    });
	
});
