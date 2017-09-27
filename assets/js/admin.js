jQuery(document).ready(function($){

	$('label[for=star-rating1]').text(function () {
		return $('label[for=star-rating1]').replaceWith('<span title="0">(0)</span>');
	});
	$('label[for=star-rating2]').text(function (){
		return $('label[for=star-rating2]').replaceWith('<span title="1"><div class="genericon genericon-star"></div></span>');
	});
	$('label[for=star-rating3]').text(function (){
		return $('label[for=star-rating3]').replaceWith('<span title="2"><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div></span>');
	});
	$('label[for=star-rating4]').text(function (){
		return $('label[for=star-rating4]').replaceWith('<span title="3"><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div></span>');
	});
	$('label[for=star-rating5]').text(function (){
		return $('label[for=star-rating5]').replaceWith('<span title="4"><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div></span>');
	});
	$('label[for=star-rating6]').text(function(){
		return $('label[for=star-rating6]').replaceWith('<span title="5"><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div><div class="genericon genericon-star"></div></span>');
	});
});