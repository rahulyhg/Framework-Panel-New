<!doctype html>
	<head>
	<script>
	(function(w,d,s,g,js,fs){
	  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
	  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
	  js.src='https://apis.google.com/js/platform.js';
	  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
	}(window,document,'script'));
	</script>

	
	
	<script>
	gapi.analytics.ready(function() {

	  /**
	   * Authorize the user immediately if the user has already granted access.
	   * If no access has been created, render an authorize button inside the
	   * element with the ID "embed-api-auth-container".
	   */
	  gapi.analytics.auth.authorize({
		container: 'embed-api-auth-container',
		clientid: '174561932350-at2c0h9qdr81thch52hqurl7l437fl5m.apps.googleusercontent.com'
	  });
	   
	  gapi.analytics.auth.authorize({
		container: 'embed-api-auth-container1',
		clientid: '174561932350-at2c0h9qdr81thch52hqurl7l437fl5m.apps.googleusercontent.com'
	  });


	  /**
	   * Create a ViewSelector for the first view to be rendered inside of an
	   * element with the id "view-selector-1-container".
	   */
	  var viewSelector1 = new gapi.analytics.ViewSelector({
		container: 'view-selector-1-container'
	  });

	  // Render both view selectors to the page.
	  viewSelector1.execute();
	


	  /**
	   * Create the first DataChart for top countries over the past 30 days.
	   * It will be rendered inside an element with the id "chart-1-container".
	   */
	  var dataChart1 = new gapi.analytics.googleCharts.DataChart({
		query: {
		  metrics: 'ga:sessions',
		  dimensions: 'ga:country',
		  'start-date': '30daysAgo',
		  'end-date': 'yesterday',
		  'max-results': 6,
		  sort: '-ga:sessions'
		},
		chart: {
		  container: 'chart-1-container',
		  type: 'PIE',
		  options: {
			width: '100%',
			pieHole: 4/9
		  }
		}
	  });



	  /**
	   * Update the first dataChart when the first view selecter is changed.
	   */
	  viewSelector1.on('change', function(ids) {
		dataChart1.set({query: {ids: ids}}).execute();
	  });
  viewSelector.on('change', function(ids) {
    dataChart.set({query: {ids: ids}}).execute();
  });

var viewSelector = new gapi.analytics.ViewSelector({
    container: 'view-selector-container'
  });

  // Render the view selector to the page.
  viewSelector.execute();


  /**
   * Create a new DataChart instance with the given query parameters
   * and Google chart options. It will be rendered inside an element
   * with the id "chart-container".
   */
  var dataChart = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:date',
      'start-date': '30daysAgo',
      'end-date': 'yesterday'
    },
    chart: {
      container: 'chart-container',
      type: 'LINE',
      options: {
        width: '100%'
      }
    }
  });



	  
	 
	});
	</script>

</head>
<body>
	<div id="embed-api-auth-container"></div>
	<div id="chart-1-container"></div>
	<div id="view-selector-1-container"></div>

	<div id="embed-api-auth-container1"></div>

	<div id="chart-container"></div>
	<div id="view-selector-container"></div>
</body>