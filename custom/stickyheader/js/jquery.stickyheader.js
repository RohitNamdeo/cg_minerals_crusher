j(function(){
	j('table').each(function() {
		if(j(this).find('thead').length > 0 && j(this).find('th').length > 0) {
			// Clone <thead>
			var jw	   = j(window),
				jt	   = j(this),
				jthead = jt.find('thead').clone(),
				jcol   = jt.find('thead, tbody').clone();

			// Add class, remove margins, reset width and wrap table
			jt
			.addClass('sticky-enabled')
			.css({
				margin: 0,
				width: '100%'
			}).wrap('<div class="sticky-wrap" />');

			if(jt.hasClass('overflow-y')) jt.removeClass('overflow-y').parent().addClass('overflow-y');

			// Create new sticky table head (basic)
			jt.after('<table class="sticky-thead" />');

			// If <tbody> contains <th>, then we create sticky column and intersect (advanced)
			if(jt.find('tbody th').length > 0) {
				jt.after('<table class="sticky-col" /><table class="sticky-intersect" />');
			}

			// Create shorthand for things
			var jstickyHead  = j(this).siblings('.sticky-thead'),
				jstickyCol   = j(this).siblings('.sticky-col'),
				jstickyInsct = j(this).siblings('.sticky-intersect'),
				jstickyWrap  = j(this).parent('.sticky-wrap');

			jstickyHead.append(jthead);

			jstickyCol
			.append(jcol)
				.find('thead th:gt(0)').remove()
				.end()
				.find('tbody td').remove();

			jstickyInsct.html('<thead><tr><th>'+jt.find('thead th:first-child').html()+'</th></tr></thead>');
			
			// Set widths
			var setWidths = function () {
					jt
					.find('thead th').each(function (i) {
						jstickyHead.find('th').eq(i).width(j(this).width());
					})
					.end()
					.find('tr').each(function (i) {
						jstickyCol.find('tr').eq(i).height(j(this).height());
					});

					// Set width of sticky table head
					jstickyHead.width(jt.width());

					// Set width of sticky table col
					jstickyCol.find('th').add(jstickyInsct.find('th')).width(jt.find('thead th').width())
				},
				repositionStickyHead = function () {
					// Return value of calculated allowance
					var allowance = calcAllowance();
				
					// Check if wrapper parent is overflowing along the y-axis
					if(jt.height() > jstickyWrap.height()) {
						// If it is overflowing (advanced layout)
						// Position sticky header based on wrapper scrollTop()
						if(jstickyWrap.scrollTop() > 0) {
							// When top of wrapping parent is out of view
							jstickyHead.add(jstickyInsct).css({
								opacity: 1,
								top: jstickyWrap.scrollTop()
							});
						} else {
							// When top of wrapping parent is in view
							jstickyHead.add(jstickyInsct).css({
								opacity: 0,
								top: 0
							});
						}
					} else {
						// If it is not overflowing (basic layout)
						// Position sticky header based on viewport scrollTop
						if(jw.scrollTop() > jt.offset().top && jw.scrollTop() < jt.offset().top + jt.outerHeight() - allowance) {
							// When top of viewport is in the table itself
							jstickyHead.add(jstickyInsct).css({
								opacity: 1,
								top: jw.scrollTop() - jt.offset().top
							});
						} else {
							// When top of viewport is above or below table
							jstickyHead.add(jstickyInsct).css({
								opacity: 0,
								top: 0
							});
						}
					}
				},
				repositionStickyCol = function () {
					if(jstickyWrap.scrollLeft() > 0) {
						// When left of wrapping parent is out of view
						jstickyCol.add(jstickyInsct).css({
							opacity: 1,
							left: jstickyWrap.scrollLeft()
						});
					} else {
						// When left of wrapping parent is in view
						jstickyCol
						.css({ opacity: 0 })
						.add(jstickyInsct).css({ left: 0 });
					}
				},
				calcAllowance = function () {
					var a = 0;
					// Calculate allowance
					jt.find('tbody tr:lt(3)').each(function () {
						a += j(this).height();
					});
					
					// Set fail safe limit (last three row might be too tall)
					// Set arbitrary limit at 0.25 of viewport height, or you can use an arbitrary pixel value
					if(a > jw.height()*0.25) {
						a = jw.height()*0.25;
					}
					
					// Add the height of sticky header
					a += jstickyHead.height();
					return a;
				};

			setWidths();

			jt.parent('.sticky-wrap').scroll(j.throttle(250, function() {
				repositionStickyHead();
				repositionStickyCol();
			}));

			jw
			.load(setWidths)
			.resize(j.debounce(250, function () {
				setWidths();
				repositionStickyHead();
				repositionStickyCol();
			}))
			.scroll(j.throttle(250, repositionStickyHead));
		}
	});
});