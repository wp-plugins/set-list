// jQuery Tabs for Plugin settings page
jQuery(document).ready(function(){

			
			var flip = 0;
				
			jQuery('#expand_options').click(function(){
				if(flip == 0){
					flip = 1;
					jQuery('#pvw_container #pvw-nav').hide();
					jQuery('#pvw_container #content').width(755);
					jQuery('#pvw_container .group').add('#pvw_container .group h2').show();
	
					jQuery(this).text('[-]');
					
				} else {
					flip = 0;
					jQuery('#pvw_container #pvw-nav').show();
					jQuery('#pvw_container #content').width(595);
					jQuery('#pvw_container .group').add('#pvw_container .group h2').hide();
					jQuery('#pvw_container .group:first').show();
					jQuery('#pvw_container #pvw-nav li').removeClass('current');
					jQuery('#pvw_container #pvw-nav li:first').addClass('current');
					
					jQuery(this).text('[+]');
				
				}
			
			});
			
				jQuery('.group').hide();
				jQuery('.group:first').fadeIn();
				
				jQuery('.group .collapsed').each(function(){
					jQuery(this).find('input:checked').parent().parent().parent().nextAll().each( 
						function(){
           					if (jQuery(this).hasClass('last')) {
           						jQuery(this).removeClass('hidden');
           						return false;
           					}
           					jQuery(this).filter('.hidden').removeClass('hidden');
           				});
           		});
           					
				jQuery('.group .collapsed input:checkbox').click(unhideHidden);
				
				function unhideHidden(){
					if (jQuery(this).attr('checked')) {
						jQuery(this).parent().parent().parent().nextAll().removeClass('hidden');
					}
					else {
						jQuery(this).parent().parent().parent().nextAll().each( 
							function(){
           						if (jQuery(this).filter('.last').length) {
           							jQuery(this).addClass('hidden');
									return false;
           						}
           						jQuery(this).addClass('hidden');
           					});
           					
					}
				}
				
				jQuery('.pvw-radio-img-img').click(function(){
					jQuery(this).parent().parent().find('.pvw-radio-img-img').removeClass('pvw-radio-img-selected');
					jQuery(this).addClass('pvw-radio-img-selected');
					
				});
				jQuery('.pvw-radio-img-label').hide();
				jQuery('.pvw-radio-img-img').show();
				jQuery('.pvw-radio-img-radio').hide();
				jQuery('#pvw-nav li:first').addClass('current');
				jQuery('#pvw-nav li a').click(function(evt){
				
						jQuery('#pvw-nav li').removeClass('current');
						jQuery(this).parent().addClass('current');
						
						var clicked_group = jQuery(this).attr('href');
		 
						jQuery('.group').hide();
						
							jQuery(clicked_group).fadeIn();
		
						evt.preventDefault();
						
					});
					
	});