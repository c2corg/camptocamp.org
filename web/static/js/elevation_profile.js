(function($) {

  var i18n = window.c2cprofile.i18n;
  var mobile = $('html').hasClass('mobile');

  // we don't use #elevation_profile_container_section_container
  var width = $('#elevation_profile_container_tbg').width();
  if (!mobile || window.innerWidth > 1000) {
    width -= 250;
    $('.elevation_profile_controls').addClass('left');
  }

  var size = { width: width, height: 300 };
  var margin = { top: 20, right: 20, bottom: 30, left: 50 };
  width = size.width - margin.left - margin.right;
  var height = size.height - margin.top - margin.bottom;

  // d3.xml() is not working well with ie9 and ie10
  // so instead, we retrieve the text and use DOMParser on it
  d3.xhr(window.c2cprofile.track, "application/xml")
    // TODO progress events?
    //.on("progress", function() {
    //  console.log(d3.event.loaded);
    //})
    .on("error", function(error) {
      alert("Something went wrong (" + error + "). Please notify us");
    })
    .get(function(error, request) {
      var greatArc = d3.geo.greatArc();
      var mode = "distance";
      var time_available;
      var data = [];
      var dtot = 0;
      var d;

      // hide loading div, displat regular ones
      $('.elevation_profile_controls').show();
      $('.elevation_profile_loading').hide();

      var parser = new DOMParser();
      var points = parser.parseFromString(request.responseText, 'text/xml')
                         .documentElement.getElementsByTagName("trkpt");

      try { 
        // some files won't have time elements. We need to handle that
        time_available = !!points[0].getElementsByTagName("time").length;

        // build data points from gpx information
        var startDate = time_available ? new Date((points[0].getElementsByTagName("time"))[0].textContent) : null;
        data.push({
          date: startDate,
          ele: +points[0].getElementsByTagName("ele")[0].textContent,
          d: 0,
          elapsed: time_available ? 0 : null
        });
        for (var i=1; i<points.length; i++) {
          d = greatArc.distance({
            source: [points[i].getAttribute("lon"), points[i].getAttribute("lat")],
            target: [points[i-1].getAttribute("lon"), points[i-1].getAttribute("lat")]
          }) * 6371;
          // it sometimes happen that SOME of the points don't have a <time>
          // In that case, we decide not to take time into account at all
          // FIXME could something better be done?
          time_available = time_available && !!points[i].getElementsByTagName("time").length;
          var date = time_available ? new Date(points[i].getElementsByTagName("time")[0].textContent) : null;
          data.push({
            date: date,
            ele: +points[i].getElementsByTagName("ele")[0].textContent,
            d: dtot + d,
            elapsed: time_available ? date - startDate : null
          });
          dtot +=d;
        }

        if (!time_available) {
          $('.xaxis-dimension').hide();
        }
      } catch(err) {
        alert('Sorry, but something went wrong! Please contact us - ' + err);
        return;
      }

      // Add an SVG element with the desired dimensions and margin
      var svg = d3.select("#elevation_profile").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

      // Scales and axes
      var x1 = d3.scale.linear()
        .range([0, width]);

      var y = d3.scale.linear()
        .range([height, 0]);
  
      var x1Axis = d3.svg.axis()
        .scale(x1)
        .orient("bottom");

      var yAxis = d3.svg.axis()
        .scale(y)
        .tickFormat(d3.format(".0f"))
        .orient("left");

      x1.domain(d3.extent(data, function(d) { return d.d; }))
        .nice();

      var yExtent = d3.extent(data, function(d) { return d.ele; });
      y.domain(yExtent)
        .nice();

      if (time_available) {
        var x2 = d3.time.scale()
          .range([0, width]);

        var x2Axis = d3.svg.axis()
          .scale(x2)
          .tickFormat(function(t) { // force display of elapsed time as hrs:mins. It is not datetime!
            return ~~(t/3600000) + ":" + d3.format("02d")(~~(t%3600000/60000));
          })
          .orient("bottom");

        x2.domain(d3.extent(data, function(d) { return d.elapsed; }))
          .nice(d3.time.hour);
      }

      svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
      .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text(i18n.yLegend);

      svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(x1Axis)
      .append("text")
        .attr("x", size.width - margin.left - margin.right)
        .attr("dy", "-.71em")
        .attr("class", "x axis legend")
        .style("text-anchor", "end")
        .text(i18n.x1Legend);

      var startLine;
      if (!mobile) {
        // horizontal line, but with same number of points
        // for eye-friendly transition
        startline = d3.svg.line()
          .x(function(d) { return x1(d.d); })
          .y(function() { return y(yExtent[0]); });
      }

      // data lines
      var dLine = d3.svg.line()
        .x(function(d) { return x1(d.d); })
        .y(function(d) { return y(d.ele); });

      if (time_available) {
        var tLine = d3.svg.line()
          .x(function(d) { return x2(d.elapsed); })
          .y(function(d) { return y(d.ele); });
      }

      // display line path
      var line = svg.append("path")
        .datum(data)
        .attr("class", "line")
        .attr("d", mobile ? dLine : startline);

      if (!mobile) {
        line.transition()
          .duration(1000)
          .attr("d", dLine);

        // Display point information one hover
        var focusv = svg.append("line")
          .attr("class", "target")
          .attr("x1", 0)
          .attr("y1", 0)
          .attr("x2", 0)
          .attr("y2", size.height - margin.bottom - margin.top)
          .style("display", "none");

        var focush = svg.append("line")
          .attr("class", "target")
          .attr("x1", 0)
          .attr("y1", 0)
          .attr("x2", size.width - margin.right - margin.left)
          .attr("y2", 0)
          .style("display", "none");

        var focus = svg.append("circle")
          .attr("class", "circle")
          .attr("r", 4.5)
          .style("display", "none");

        var bubble = svg.append("text")
          .attr("x", (size.width - margin.left - margin.right) / 2)
          .attr("dy", "-.71em")
          .attr("class", "bubble")
          .style("text-anchor", "middle")
          .style("display", "none")
          .text("");

        svg.append("rect")
          .attr("class", "overlay")
          .attr("width", width)
          .attr("height", height)
        .on("mouseover", function() {
          focus.style("display", null);
          focush.style("display", null);
          focusv.style("display", null);
          bubble.style("display", null);
        })
        .on("mouseout", function() {
          focus.style("display", "none");
          focush.style("display", "none");
          focusv.style("display", "none");
          bubble.style("display", "none");
        })
        .on("mousemove", mousemove);
      }

      var bisectDistance = d3.bisector(function(d) { return d.d; }).left;
      var bisectDate = d3.bisector(function(d) { return d.elapsed; }).left;
      var formatDistance = d3.format(".2f");
      var formatDate = d3.time.format("%H:%M");
      var formatMinutes = d3.format("02d");

      function mousemove() {
        var bisect = (mode === "distance") ? bisectDistance : bisectDate;
        var x0 = (mode === "distance") ? x1.invert(d3.mouse(this)[0])
                                     : x2.invert(d3.mouse(this)[0]);
        var i = bisect(data, x0, 1, data.length - 1);
        var d0 = data[i - 1];
        var d1 = data[i];
        var d = (mode === "distance") ? (x0 - d0.d > d1.d - x0 ? d1 : d0)
                                      : (x0 - d0.elapsed > d1.elapsed - x0 ? d1 : d0);

        var dy = y(d.ele);
        var dx = (mode === "distance") ? x1(d.d) : x2(d.elapsed);

        focus.attr("transform", "translate(" + dx + "," + dy + ")");
        focush.attr("transform", "translate(0," + dy + ")");
        focusv.attr("transform", "translate(" + dx + ",0)");

        if (time_available) {
          var elapsed = d.elapsed/1000;
          bubble.text(i18n.elevation + " " + d.ele + i18n.meters + " / " + i18n.distance + " " + formatDistance(d.d) + 
            i18n.kilometers + " / " + i18n.time + " " + formatDate(d.date) + " / " + i18n.duration + " " +
            ~~(elapsed/3600) + ":" +formatMinutes(~~(elapsed%3600/60)));
        } else {
          bubble.text(i18n.elevation + " " + d.ele + i18n.meters + " / " + i18n.distance + " " +
            formatDistance(d.d) + i18n.kilometers);
        }
      }

      // switch xAxis between distance and time
      if (time_available) {
        d3.selectAll("input[name=profile_mode]").on("change", function() {
          var nLine = (this.value === "distance") ? dLine : tLine;
          var axis = (this.value === "distance") ? x1Axis : x2Axis;
          var legend = (this.value === "distance") ? i18n.x1Legend : i18n.x2Legend;
          mode = this.value;

          if (mobile) {
            line.attr("d", nLine);
          } else {
            line.transition()
              .duration(1000)
              .attr("d", nLine);
          }

          d3.select(".x.axis")
            .call(axis);
          d3.select(".x.axis.legend")
            .text(legend);
        });
      }
    });

})(jQuery);
