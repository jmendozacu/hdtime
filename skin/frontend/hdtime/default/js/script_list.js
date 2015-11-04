jQuery.noConflict();

jQuery(document).ready(function(){
	
	jQuery('a.link-compare').click(function(){
		var name = jQuery(this).closest('.product-shop').find('.product-name a').html();

		var localStorage = window.localStorage;
		localStorage.setItem('nameCompare', name);

	});
	
});