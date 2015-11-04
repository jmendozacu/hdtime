jQuery.noConflict();

jQuery(document).ready(function(){
	
	jQuery('a.btn-compare').click(function(){
		var name = jQuery(this).closest('.product-shop').find('.prod-header').html();
console.log(name)
		var localStorage = window.localStorage;
		localStorage.setItem('nameCompare', name);

	});
	
});