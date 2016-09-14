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
	
	// repeated post meta group
	$('.meta_box_repeatable_row div').hide();
	/*
	$('.meta_box_repeatable_row input[type="checkbox"]').on('change', function() {
   		$('#' + this.id + '_div_open').toggle( this.checked ); 
	});
	*/
	
	
	$('.meta_box_repeatable_row .toggle').toggle(function() {
    	$('#' + this.id + '_wrap').show();
		//$(this).parent().next('.toggle_div').show();
    	$(this).html('Less');
	}, function() {
    	$('#' + this.id + '_wrap').hide();
    	$(this).html(this.id);
	});
	
	
});
