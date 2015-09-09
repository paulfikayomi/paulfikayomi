<!DOCTYPE html>
<html>
<head>
  <title>Donut-Pie Chart</title>
  
  <style type="text/css">
  #bord{
    border: 5px dashed green;
  }
  #chart {
  	height: 360px;
  	margin: 0 auto;
  	position: relative;
  	width: 360px;
  }
  .tooltip {
  	background: #eee;
  	box-shadow: 0 0 5px #999999;
  	color: #333;
  	display: none;
  	font-size: 12px;
  	left: 130px;
  	padding: 10px;
  	position: absolute;
  	text-align: center;
  	top: 95px;
  	width: 80px;
  	z-index: 10;
  }

  .legend {

  	font-size: 12px;
  }
  rect {
  	cursor: pointer;
  	stroke-width: 2;
  }
  rect.disabled {
  	fill: transparent !important;
  }
  h1 {
  	font-size: 14px;
  	text-align: center;
  }
  </style>
</head>
<body>
<div id="bord">
<h1>Silverbird Gallery Tickets in August 2015 </h1>
<div id="chart"></div>
</div>

<script  src="d3.min.js"></script>

<script>
  (function(d3){
    'use strict';

    /**var dataset = [ 
      {label: 'Abuja', count: 10 },
      {label: 'Bayelsa', count: 20 },
      {label: 'Lagos', count: 30 },
      {label: 'Delta', count: 40 },
      {label: 'Niger', count: 50 },
    ];*/

    var width = 360;
    var height = 360;
    var radius = Math.min(width, height) / 2;
    var donutWidth = 75;
    var legendRectSize = 18;
    var legendSpacing = 4;

    var color = d3.scale.category20b();

    var svg = d3.select('#chart')
      .append('svg')
      .attr('width', width)
      .attr('height', height)
      .append('g')
      .attr('transform', 'translate(' + (width / 2) + ',' + (height / 2) + ')');

    var arc = d3.svg.arc()
    	.innerRadius(radius - donutWidth)
      .outerRadius(radius);

    var pie = d3.layout.pie()
      .value(function(d) { return d.count; })
      .sort(null);

    var tooltip = d3.select('#chart')
    	.append('div')
    	.attr('class', 'tooltip');

    	tooltip.append('div')
    		.attr('class', 'label');

    	tooltip.append('div')
    		.attr('class', 'count');

    	tooltip.append('div')
    		.attr('class', 'percent');



     d3.csv('weekdays.csv', function(error, dataset){
     	dataset.forEach(function(d){
     		d.count = +d.count;
     		d.enabled = true;
     });

    var path = svg.selectAll('path')
    .data(pie(dataset))
    .enter()
    .append('path')
    .attr('d', arc)
    .attr('fill', function(d, i){
      return color(d.data.label);
    })
    .each(function(d) { this._current = d; });

    path.on('mouseover', function(d){
    	var total = d3.sum(dataset.map(function(d) {
    		return (d.enabled) ? d.count : 0;
    	}));
    	var percent = Math.round(1000* d.data.count / total) / 10;
    	tooltip.select(' .label').html(d.data.label);
    	tooltip. select(' .count').html(d.data.count);
    	tooltip.select('.percent').html(percent + '%');
    	tooltip.style('display', 'block');
    });

    path.on('mouseout', function() {
    	tooltip.style('display', 'none');
    })

    /**path.on('mousemove', function(d){
    	tooltip.style('top', (d3.event.pageY + 10) + 'px')
    		.style('left', (d3.event.pageX + 10) + 'px');
    }); */

    var legend = svg.selectAll('.legend')
    .data(color.domain())
    .enter()
    .append('g')
    .attr('transform', function(d, i) {
    	var height = legendRectSize + legendSpacing;
    	var offset = height * color.domain().length / 2;
    	var horz = -2 * legendRectSize;
    	var vert =i * height - offset;
    	return 'translate(' + horz + ',' + vert + ')';
    });

    legend.append('rect')
    	.attr('width', legendRectSize)
    	.attr('height', legendRectSize)
    	.style('fill', color)
    	.style('stroke', color)
    	.on('click', function(label) {
    		var rect = d3.select(this);
    		var enabled = true;
    		var totalEnabled = d3.sum(dataset.map(function(d){
    			return (d.enabled) ? 1 : 0;
    		}));

    		if (rect.attr('class') === 'disabled') {
    			rect.attr('class', '');
    		} else {
    			if (totalEnabled < 2) return;
    			rect.attr('class', 'disabled');
    			enabled = false;
    		}

    		pie.value(function(d) {
    			if (d.label === label) d.enabled = enabled;
    			return (d.enabled) ? d.count : 0;

    		});

    		path = path.data(pie(dataset));

    		path.transition()
    			.duration(750)
    			.attrTween('d', function(d) {
    				var interpolate = d3.interpolate(this._current, d);
    				this._current = interpolate(0);
    				return function(t) {
    					return arc(interpolate(t));
    				};
    			});
    	});

    legend.append('text')
    	.attr('x', legendRectSize + legendSpacing)
    	.attr('y', legendRectSize - legendSpacing)
    	.text(function(d) { return d; });

    });

  })(window.d3);
</script>

</body>
</html>