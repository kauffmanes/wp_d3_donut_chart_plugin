var chartLabel, ease, title,containerWidth, containerHeight, innerRadius, outerRadius, view, tween,
	arc, arcs, arcHover,bgGroup, outerBackground, innerBackground, pie, path, data, rendered;

var chart = window.CHART_DATA || {};

chartLabel = chart.chartLabel || "Untitled";
containerWidth = 246;
containerHeight = 246;
innerRadius = 60;
outerRadius = containerWidth / 2 - 20;
tween = 450;
// data = chart.options && chart.options.data || {};
configFile = chart.options && chart.options.configFile;
rendered = false;

//find the view, append the child elements that we need
view = d3.select(document.getElementById('donut-container'))
	.attr('class', 'donut-chart')
	.append('svg') //add svg container
	.attr('width', containerWidth)
	.attr('height', chartLabel ? containerHeight + 30 : containerHeight) //only make it 30px taller if we need space for a label
	.append('g')
	.attr('transform','translate(' + containerWidth / 2 + ',' + containerHeight / 2 + ')');

//"title" is the overall chart label
title = view.append('svg:g')
	.attr('transform','translate(0' + ',' + containerHeight / 2 +')');

title.append('svg:text')
	.attr('class', 'chart-label')
	.attr('dy', 5)
	.attr('fill', '#39454e')
	.attr('style', 'font-weight:bold;')
	.text(chartLabel || '')
	.attr('text-anchor','middle');

//make the paths!
arc = d3.arc()
	.outerRadius(outerRadius)
	.innerRadius(innerRadius);

bgGroup = view.append('svg:g')
	.attr('class', 'center-group')
	.attr('transform','translate(' + 0 + ',' + 0 + ')');

outerBackground = bgGroup.append('svg:circle')
	.attr('fill','rgba(0,0,0,.1)')
	.attr('r', outerRadius);

innerBackground = bgGroup.append('svg:circle')
	.attr('fill', '#fff')
	.attr('r', innerRadius);

//when hovering on arc
arcHover = d3.arc()
	.outerRadius(outerRadius + 5)
	.innerRadius(innerRadius + 5);

pie = d3.pie()
	.value(function (d) {return d.value || 0; })
	.sort(null);

//Builds the donut and displays it on the page
render = function () {

	// d3.csv(configFile, function (data) {

		if (!data || !data.length) {
			return;
		}

		//select all g elements that have slice class
		arcs = view.datum(data).selectAll('g.slice')

			//associate pie data
			.data(pie)

			//create g elements for each piece of data
			.enter()

			//create a group to associate slice so we can add labels to each slice
			.append('svg:g')

			//slice stylin'
			.attr('class','slice');

		//draws the paths for slices
		path = arcs.append('path')

			.attr('fill', function(d, idx) {
				return data[idx].color;
			})

			.attr('d', arc)

			.each(function (d) {this._current = d; });

		drawLabels({ value: 0 || '', label: 'Total' }); //todo - customize this

		addListeners();

		rendered = true;

	//});

};

drawLabels = function(dataOptions) {

	var labelGroup;

	//remove label and redraw so labels don't stack on top of each other
	view.select('.label-group')
		.remove();

	labelGroup = view.append('svg:g')
		.attr('class', 'label-group')
		.attr('transform','translate(' + 0 + ',' + 0 + ')');

	//Render label
	labelGroup.append('svg:text')
		.attr('class', 'label')
		.attr('dy', -2)
		.attr('text-anchor','middle')
		.attr('fill', '#39454e')
		.attr('style', 'font-weight:bold;')
		.text(dataOptions.label || '');

	//Render Label Value
	labelGroup.append('svg:text')
		.attr('class', 'label-value')
		.attr('dy', 15)
		.attr('text-anchor','middle')
		.attr('fill', '#888')
		.text((chart.options.preUnitLabel || '') + (dataOptions.value || 0) + (chart.options.postUnitLabel || ''));

};

//adds hover/click events
addListeners = function() {

	//when you hover on a slice, make it look like it zooms
	arcs.on('mouseover',function (d, idx) {

		var target = d3.select(this);

		arcHover = d3.arc().outerRadius(outerRadius + 5).innerRadius(innerRadius + 5);

		drawLabels(data[idx]);

		target.select('path').transition()
			//.ease('elastic')
			//.duration(tween)
			.attr('d', arcHover)
			.attr('fill',function (d) {
				return d3.rgb(data[idx].color).brighter();
			});

	});

	//return to normal
	arcs.on('mouseout',function (d, idx) {

		var target = d3.select(this);

		drawLabels({ value: 0 || '', label:'Total' });

		target.select('path').transition()
			//.ease('back')
			//.duration(tween)
			.attr('d', arc)
			.attr('fill',function (d) { return d3.rgb(data[idx].color); });

	});

};

if (!rendered) {
	d3.csv(configFile, function (res) {
		data = res;
		render();
	});
}