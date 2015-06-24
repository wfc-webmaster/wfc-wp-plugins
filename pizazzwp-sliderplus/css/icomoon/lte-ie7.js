/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'icomoon\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-arrow-left': '&#xf489;',
		'icon-arrow-right': '&#xf488;',
		'icon-arrow-right-2': '&#xf473;',
		'icon-arrow-left-2': '&#xf472;',
		'icon-arrow-right-3': '&#xe00a;',
		'icon-arrow-left-3': '&#xe00b;',
		'icon-arrow-left-4': '&#xf060;',
		'icon-arrow-right-4': '&#xf061;',
		'icon-caret-left': '&#xf0d9;',
		'icon-caret-right': '&#xf0da;',
		'icon-arrow-left-5': '&#xe00c;',
		'icon-arrow-right-5': '&#xe00d;',
		'icon-arrow-left-6': '&#xe014;',
		'icon-arrow-right-6': '&#xe015;',
		'icon-arrow-left-7': '&#xe018;',
		'icon-arrow-right-7': '&#xe019;',
		'icon-arrow-left-8': '&#xe010;',
		'icon-arrow-right-8': '&#xe011;',
		'icon-right': '&#xf304;',
		'icon-left': '&#xf305;',
		'icon-arrow-right-9': '&#xe002;',
		'icon-arrow-left-9': '&#xe003;',
		'icon-arrow-left-10': '&#xe006;',
		'icon-arrow-right-10': '&#xe007;',
		'icon-arrow': '&#xe000;',
		'icon-arrow-2': '&#xe01d;'
	},
	els = document.getElementsByTagName('*'),
					i, attr, html, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if (!el) {
			break;
		}
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};