jQuery(document).ready(function() {
	jQuery('.pz-help').qtip({
		content: 'Click to show/hide help',
		position: {
			at: 'top right',
			my: 'left bottom',
			adjust: {y: 20}
		},
		show: {delay: 1500},
		style: {
			classes: 'ui-tooltip ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-pizazz'

		}
	});

	jQuery('.pz-help span.pz-help-button').qtip({
		position: {
			at: 'left center',
			my: 'right middle',
			adjust: {
				x: -10,
				resize: true
			},
			viewport: true

		},
		content: {
			text: function(api) {
				return jQuery(this).clone().children('.pz-help-text')
			},
			title: {
				button: 'Close',
				text: function(api) {
					return jQuery(this).parent().prev().clone().text()
				}
			}
		},
		show: {
			event: 'click',
			solo: true
		},
		hide: {
			event: ' click'
		},
		style: {
			classes: 'ui-tooltip-light ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-pizazz',
			width: '400px'

		}
	});
});