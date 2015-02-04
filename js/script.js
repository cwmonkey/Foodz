(function($) {

var $html = $('html')
			.removeClass('no-js')
			.addClass('js');
var $body = $('body');

$body
	.delegate('.action.save', 'click', function(e) {
		e.preventDefault();
		var $this = $(this);
		var $place = $this.closest('.place');

		$place.animate({opacity: 0}, 1000, function() {
			$place.detach();
		});

		$.ajax({
			url: $this.attr('href'),
			type: 'get'
		})
		.done(function(data) {
			var $div = $('<div/>').html(data);
			$('#saved').html($div.find('#saved').html());
		})
		.fail(function() {
			document.location = $this.attr('href');
			return;
		})
		;
	})
	.delegate('.action.unsave', 'click', function(e) {
		e.preventDefault();
		var $this = $(this);

		var times = $this.data('times') || 1;
		if ( times < 2 ) {
			$this.data('times', 2);
			return;
		}

		var $place = $this.closest('.place');

		$place.animate({opacity: 0}, 1000, function() {
			$place.detach();
		});

		$.ajax({
			url: $this.attr('href'),
			type: 'get'
		})
		.done(function(data) {
			var $div = $('<div/>').html(data);
			$('#saved').html($div.find('#saved').html());

			$.ajax({
				url: document.location.href,
				type: 'get'
			})
			.done(function(data) {
				var $div = $('<div/>').html(data);
				$('#searched').html($div.find('#searched').html());
			})
			;
		})
		.fail(function() {
			document.location = $this.attr('href');
			return;
		})
		;
	})
	.delegate('.action.visited', 'click', function(e) {
		e.preventDefault();
		var $this = $(this);

		$.ajax({
			url: $this.attr('href'),
			type: 'get'
		})
		.done(function(data) {
			var $div = $('<div/>').html(data);
			$('#saved').html($div.find('#saved').html());
		})
		.fail(function() {
			document.location = $this.attr('href');
			return;
		})
		;
	})
	;

$('#search').floatLabels({removePlaceholder: false});

})(jQuery);