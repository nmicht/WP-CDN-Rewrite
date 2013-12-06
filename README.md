WP CDN Rewrite
===========

Wordpress Plugin to rewrite all the assets to get them from CDN instead the normal location.  

Installation
1. Download the file and include it in your plugins folder  
2. In the wp-config file add the url for you CDN without the trailing slash  
<pre>
 	//Setting the CDN url in Edgecast
	define('CDN_URL','http://assets.michelletorres.mx');
</pre>
