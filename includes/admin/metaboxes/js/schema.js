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
	
});