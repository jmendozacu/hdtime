jQuery.noConflict();

jQuery(document).ready(function(){
	
	jQuery('a.link-compare').click(function(){
		var name = jQuery(this).closest('.prod-item').find('.discr .title a').html();

		var localStorage = window.localStorage;
		localStorage.setItem('nameCompare', name);

	});
	
});