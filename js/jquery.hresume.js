(function ($) {
	function lenient_date_parser(date) {
		var datex = /(\d\d\d\d)(?:(?:-(\d\d))?-(\d\d))?/;
		
		if(date) {
			var p_date = datex.exec(date);
			var yyyy = p_date[1];
			var mm = ((p_date[2])? (p_date[2] - 1): 0);
			var dd = (p_date[3]? p_date[3] : 1);
			return (new Date(yyyy, mm ,dd)).getTime();
		} else {
			return null;
		}
	}
	
	$.fn.enhance_resume = function () {
		this.each(function (index, item) {
			// get all the nodes needed before the resume is emptied
			
			// the contact information should be the first vcard
			var contact = $(".vcard", item)[0];
			var lat = $(".geo .latitude", contact).attr("title");
			var lng = $(".geo .longitude", contact).attr("title");
			// get all the vevents
			var events = $(".vevent",item);
			
			// empty everything to start with a blank slate
			//$(item).empty();
			
			// display the formatted nane
			$(item).append($("<h1></h1>").html($(".fn", contact).html()));
			
			if(OpenLayers) {  // check if mapping is available
				const zoom = 16;
				$(item).append($("<div></div>").attr("id", "map"));
				
				map = new OpenLayers.Map ("map", {
					controls:[
						new OpenLayers.Control.Navigation(),
						new OpenLayers.Control.PanZoomBar(),
						new OpenLayers.Control.LayerSwitcher(),
						new OpenLayers.Control.Attribution()],
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
								maxResolution: 156543.0399,
					numZoomLevels: 19,
					units: 'm',
					projection: new OpenLayers.Projection("EPSG:900913"),
					displayProjection: new OpenLayers.Projection("EPSG:4326")
				} );
				
				// Define the map layer
				// Note that we use a predefined layer that will be
				// kept up to date with URL changes
				// Here we define just one layer, but providing a choice
				// of several layers is also quite simple
				// Other defined layers are OpenLayers.Layer.OSM.Mapnik, OpenLayers.Layer.OSM.Maplint and OpenLayers.Layer.OSM.CycleMap
				layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
				map.addLayer(layerMapnik);
				layerTilesAtHome = new OpenLayers.Layer.OSM.Osmarender("Osmarender");
				map.addLayer(layerTilesAtHome);
				layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
				map.addLayer(layerCycleMap);
				layerMarkers = new OpenLayers.Layer.Markers("Markers");
				map.addLayer(layerMarkers);
	 
				var lonLat = new OpenLayers.LonLat(lng, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				map.setCenter (lonLat, zoom);
	 
				var size = new OpenLayers.Size(21,25);
				var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
				var icon = new OpenLayers.Icon('http://www.openstreetmap.org/openlayers/img/marker.png',size,offset);
				layerMarkers.addMarker(new OpenLayers.Marker(lonLat,icon));
			}
			
			//console.log(events.length);
			
			if(events.length > 0) {
				var events_reduced = [];
				var timeline = $("<div></div>").attr("id", "timeline");
				
				//console.log(events);
				
				events.each(function (idex, item) {
					var p_dtstart = lenient_date_parser($(".dtstart", item).attr("title"));
					var p_dtend = lenient_date_parser($(".dtend ", item).attr("title"));
					
					//console.log(p_dtstart, p_dtend);
					
					if(p_dtstart != null) {
						events_reduced.push({
							title: $(".org", item).html(),
							
							dtstart: p_dtstart, 
							dtend: ((p_dtend != null)?p_dtend : new Date().getTime())
						});
					} else {
						console.log($(".dtstart", item));
					}
				});
				
				//console.log(events_reduced);
				
				// ideally we should be using an insertion sort above
				events_reduced.sort(function (a,b) {
					return a.dtstart - b.dtstart;
				});
				
				// use a line sweep algorithm to detect overlapping employment
				const seconds_per_pixel = 100000000;
				const height = 50;
				var origin = events_reduced[0].dtstart;
				var start_time = 0;
				var end_time_queue = [];
				$.each(events_reduced, function(index, item) {
					var insert_position = null;
					start_time = item.dtstart;
					
					//console.log(start_time, end_time_queue, item.title);
					
					//end_time_queue = $.grep(end_time_queue,function (i) {
					//	return i > start_time;
					//});
					
					$.each(end_time_queue, function (dex,tem) {
						
						// have we passed it
						if(tem <= start_time){
							end_time_queue[dex] = undefined;
						}
						
						// find the insert position
						if(insert_position == null && end_time_queue[dex] == undefined) { // dont trust tem its out of sync after the stuff above
							insert_position = dex;
						}
					});
					
					//console.log(insert_position);
					
					if(insert_position == null) {
						insert_position = end_time_queue.length;
					}
					
					end_time_queue[insert_position] = item.dtend;
					
					timeline.append(
						$("<div></div>").css({
							position: "absolute",
							top: height * insert_position,
							left: (item.dtstart - origin) / seconds_per_pixel,
							height: height,
							width: (item.dtend - item.dtstart) / seconds_per_pixel
						}).append(
							$("<div></div>").addClass("time-span").html(item.title)
						)
					);
					
				});
				// output the timeline
				$(item).append(timeline);
				
				//console.log(events_reduced, events_reduced.length);
			}
		});
	};
})(jQuery)