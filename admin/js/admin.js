(function ($) {
	'use strict';

	var cfg = window.veilaAdmin || {};

	function val(id, fallback) {
		var $el = $('#' + id);
		if (!$el.length) {
			return fallback === undefined ? '' : fallback;
		}
		if ($el.is(':checkbox')) {
			return $el.is(':checked');
		}
		if ($el.is('fieldset')) {
			return $el.find('input:checked').val() || fallback;
		}
		return $el.val();
	}

	function hexLum(hex) {
		if (!hex) {
			return null;
		}
		hex = String(hex).replace('#', '');
		if (hex.length === 3) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}
		if (!/^[0-9a-fA-F]{6}$/.test(hex)) {
			return null;
		}
		var chan = [];
		for (var i = 0; i < 3; i++) {
			var c = parseInt(hex.substr(i * 2, 2), 16) / 255;
			chan.push(c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4));
		}
		return 0.2126 * chan[0] + 0.7152 * chan[1] + 0.0722 * chan[2];
	}

	function ratio(a, b) {
		var la = hexLum(a), lb = hexLum(b);
		if (la === null || lb === null) {
			return null;
		}
		return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
	}

	function mediaUrl(target) {
		var $w = $('.veila-media-field[data-target="' + target + '"]');
		return $w.data('url') || '';
	}

	// Mirrors Veila_Settings::background_is_dark() and surface_color().
	function surface(st) {
		if (st.layout === 'card' && st.card) {
			return st.card;
		}
		if (st.bgType === 'image' && st.bgUrl && st.layout !== 'split') {
			return '#000000';
		}
		return st.bg1 || '#FFFFFF';
	}

	function isDark(st) {
		if (st.layout === 'card' && st.card) {
			var lc = hexLum(st.card);
			return lc !== null && lc < 0.45;
		}
		if (st.bgType === 'image' && st.bgUrl && st.layout !== 'split') {
			return true;
		}
		var l1 = hexLum(st.bg1);
		if (l1 === null) {
			return false;
		}
		if (st.bgType === 'gradient' && st.bg2) {
			var l2 = hexLum(st.bg2);
			if (l2 !== null) {
				return ((l1 + l2) / 2) < 0.45;
			}
		}
		return l1 < 0.45;
	}

	function readable(chosen, st, min) {
		var r = ratio(chosen, surface(st));
		if (chosen && r !== null && r >= min) {
			return chosen;
		}
		return isDark(st) ? '#F8F7F4' : '#1A1A1A';
	}

	function onColor(accent) {
		var w = ratio(accent, '#FFFFFF'), b = ratio(accent, '#111111');
		if (w === null) {
			return '#FFFFFF';
		}
		return w >= b ? '#FFFFFF' : '#111111';
	}

	function state() {
		return {
			layout: val('layout', 'centered'),
			align: val('text_align', 'center'),
			bgType: val('bg_type', 'color'),
			bg1: val('bg_color'),
			bg2: val('bg_color_2'),
			angle: val('bg_gradient_angle', 160),
			bgUrl: mediaUrl('bg_image_id'),
			overlay: parseInt(val('overlay_opacity', 40), 10) || 0,
			text: val('text_color'),
			heading: val('heading_color'),
			accent: val('accent_color') || '#1B2A4A',
			card: val('card_bg'),
			font: val('font_stack', 'serif'),
			hsize: parseInt(val('heading_size', 44), 10) || 44,
			logoW: parseInt(val('logo_width', 160), 10) || 160
		};
	}

	function updatePreview() {
		var $root = $('#veila-preview-root');
		if (!$root.length) {
			return;
		}
		var st = state();
		var root = $root[0];
		var bg1 = st.bg1 || '#FFFFFF';
		var bg;
		if (st.bgType === 'gradient') {
			bg = 'linear-gradient(' + st.angle + 'deg,' + bg1 + ',' + (st.bg2 || bg1) + ')';
		} else if (st.bgType === 'image' && st.bgUrl && st.layout !== 'split') {
			bg = bg1 + ' url("' + st.bgUrl + '") center/cover no-repeat';
		} else {
			bg = bg1;
		}
		var hasImage = st.bgType === 'image' && !!st.bgUrl;
		// The footer sits on the page background, outside the card.
		var pageSt = $.extend({}, st, { layout: st.layout === 'card' ? 'centered' : st.layout });
		var stack = (cfg.stacks && cfg.stacks[st.font]) ? cfg.stacks[st.font] : { stack: 'Georgia,serif', head: 'Georgia,serif' };
		var vars = {
			'--veila-bg': bg,
			'--veila-media': hasImage ? 'url("' + st.bgUrl + '")' : 'none',
			'--veila-overlay': (hasImage && st.layout !== 'split') ? String(st.overlay / 100) : '0',
			'--veila-surface': surface(st),
			'--veila-text': readable(st.text, st, 4.5),
			'--veila-heading': readable(st.heading, st, 3),
			'--veila-accent': st.accent,
			'--veila-on-accent': onColor(st.accent),
			'--veila-link': readable(st.accent, st, 3),
			'--veila-foot': readable(st.text, pageSt, 4.5),
			'--veila-foot-link': readable(st.accent, pageSt, 3),
			'--veila-card': st.card || '#FFFFFF',
			'--veila-font': stack.stack,
			'--veila-head-font': stack.head || stack.stack,
			'--veila-h-size': st.hsize + 'px',
			'--veila-logo-w': st.logoW + 'px'
		};
		$.each(vars, function (k, v) {
			root.style.setProperty(k, v);
		});

		root.className = root.className.replace(/\bveila-(layout|align)-[a-z]+\b/g, '').replace(/\bveila-is-(dark|light)\b/g, '').replace(/\bveila-has-image\b/g, '').replace(/\s+/g, ' ').trim();
		$root.addClass('veila-layout-' + st.layout + ' veila-align-' + st.align + ' ' + (isDark(st) ? 'veila-is-dark' : 'veila-is-light') + (hasImage ? ' veila-has-image' : ''));

		// Logo: dark-background variant when the surface is dark.
		var light = mediaUrl('logo_id'), dark = mediaUrl('logo_id_dark');
		var logo = (isDark(st) && dark) ? dark : (light || dark);
		var $img = $root.find('.veila-logo img');
		if (logo) {
			$img.attr('src', logo).prop('hidden', false);
			$root.find('.veila-wordmark').prop('hidden', true);
		} else {
			$img.prop('hidden', true);
			$root.find('.veila-wordmark').prop('hidden', false);
		}

		// Text.
		var mode = val('mode', 'coming_soon');
		var badge = val('badge');
		if (!badge) {
			badge = (mode === 'maintenance') ? cfg.badges.maintenance : cfg.badges.coming_soon;
		}
		$root.find('.veila-badge').text(badge).prop('hidden', !val('show_badge'));
		$root.find('.veila-headline').text(val('headline'));
		var msg = $('<div>').text(String(val('message')).replace(/<[^>]*>/g, '')).html().replace(/\n{2,}/g, '</p><p>').replace(/\n/g, '<br>');
		$root.find('.veila-message').html(msg ? '<p>' + msg + '</p>' : '');

		// Social icons.
		$root.find('.veila-social li').each(function () {
			var net = (this.className.match(/veila-social-([a-z]+)/) || [])[1];
			if (net) {
				$(this).prop('hidden', !val('social_' + net));
			}
		});

		// Footer.
		var footerText = val('footer_text');
		var $ft = $root.find('.veila-footer-text');
		if (footerText) {
			if (!$ft.length) {
				$ft = $('<span class="veila-footer-text"></span>').prependTo($root.find('.veila-footer'));
			}
			$ft.text(footerText).prop('hidden', false);
		} else {
			$ft.prop('hidden', true);
		}
		$root.find('.veila-login-link').prop('hidden', !val('show_login_link'));
		$root.find('.veila-credit').prop('hidden', !val('show_credit'));

		// Contrast hint (WCAG AA for body text).
		var r = ratio(st.text, surface(st));
		var $c = $('#veila-contrast');
		if (st.text && r !== null && r < 4.5) {
			$c.addClass('is-low').text(cfg.contrastLow + ' (' + r.toFixed(1) + ':1)');
		} else if (r !== null) {
			$c.removeClass('is-low').text(cfg.contrastOk + ' (' + r.toFixed(1) + ':1)');
		} else {
			$c.removeClass('is-low').text('');
		}

		$(document).trigger('veila:preview', [root, st]);
	}

	window.veilaUpdatePreview = updatePreview;

	$(function () {
		if ($.fn.wpColorPicker) {
			$('.veila-color').wpColorPicker({
				change: function () {
					setTimeout(updatePreview, 0);
				},
				clear: function () {
					setTimeout(updatePreview, 0);
				}
			});
		}
		$('.veila-form').on('input change', 'input, select, textarea', updatePreview);

		var freeTabs = {};
		$.each(cfg.freeTabs || [], function (i, t) {
			freeTabs[t] = true;
		});
		function activateTab(tab) {
			if (!tab || !$('#veila-tab-' + tab).length) {
				return;
			}
			$('.veila-tab').removeClass('is-active').removeAttr('aria-current');
			$('.veila-tab[data-tab="' + tab + '"]').addClass('is-active').attr('aria-current', 'page');
			$('.veila-panel').removeClass('is-active');
			$('#veila-tab-' + tab).addClass('is-active');
			$('.veila-form > p.submit').toggle(!!freeTabs[tab]);
			$('.veila-active-tab').val(tab);
		}
		$('.veila-wrap form').on('submit', function () {
			var tab = $('.veila-tab.is-active').data('tab') || '';
			var $field = $(this).find('.veila-active-tab');
			if (!$field.length) {
				$field = $('<input type="hidden" class="veila-active-tab" name="veila_active_tab" />').appendTo(this);
			}
			$field.val(tab);
		});
		$('.veila-tab').on('click', function (e) {
			e.preventDefault();
			var tab = $(this).data('tab');
			activateTab(tab);
			if (window.history && history.replaceState) {
				history.replaceState(null, '', '#veila-tab-' + tab);
			}
		});
		var hashMatch = (window.location.hash || '').match(/^#veila-tab-([A-Za-z0-9_-]+)/);
		activateTab(hashMatch ? hashMatch[1] : 'status');
		if (hashMatch) {
			// The panel id matches the hash, so the browser jumps to it. Keep the tab bar in view instead.
			setTimeout(function () {
				window.scrollTo(0, 0);
			}, 0);
		}

		$('.veila-media-select').on('click', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('.veila-media-field');
			var frame = wp.media({
				title: cfg.mediaTitle,
				button: { text: cfg.mediaButton },
				library: { type: 'image' },
				multiple: false
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				$wrap.find('input[type="hidden"]').val(att.id);
				$wrap.data('url', att.url);
				$wrap.find('.veila-media-preview').empty().append($('<img alt="">').attr('src', att.url));
				updatePreview();
			});
			frame.open();
		});
		$('.veila-media-clear').on('click', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('.veila-media-field');
			$wrap.find('input[type="hidden"]').val('');
			$wrap.data('url', '');
			$wrap.find('.veila-media-preview').empty();
			updatePreview();
		});

		$('#veila-dismiss-upsell').on('click', function () {
			$.post(window.ajaxurl, { action: 'veila_dismiss_upsell', nonce: $(this).data('nonce') }).always(function () {
				$('#veila-upsell').slideUp(150);
			});
		});

		updatePreview();
	});
})(jQuery);
