<?php

class WP_CDNRewrites
{
    var $host;
    var $cdn;
    var $options = array();
	
    /**
    * The CDN_URL is taken from wp-config
    */
    function __construct()
    {   
        $this -> host  = 'http://'.$_SERVER['HTTP_HOST'];
        $this -> cdn   = CDN_URL;
    }
    
    /**
	 * Function to activate the plugin
	 */ 
    function activate() {
        $pluginOptions = get_option('wpcdn_options');
    
        if ( !$pluginOptions ) {
            add_option ( 'wpcdn_options' , $this -> options );
        }
    }
    
	/**
	 * Erase options data when deactivate 
	 */
    function deactivation() {
        delete_option ( 'wpcdn_options' );
        unregister_setting ( 'wpcdn_settings' , 'wpcdn_options' );
    }
	
	/**
	 * Create the admin page options
	 */ 
    function adminmenu() {
        add_options_page('WP CDN', 'WP CDN', 'manage_options', 'wpcdn', array($this,'configPage'));
    }
	
	/**
	 * Set the errors options
	 */ 
    function addNotices() {
        settings_errors('wpcdn_errors');
    }
	
	/**
	 * Define the set of options
	 */
    function init(){
        register_setting ( 'wpcdn_settings' , 'wpcdn_options' , array($this,'sanitize'));
    }
	
	/**
     *  Function to clean the inputs
     *  @param array $input from the form
     *  @return array with clean inputs
     */
    function sanitize($input){               
        add_settings_error(
            'wpcdn_errors',
            'wpcdn_success',
            'Your configuration is saved',
            'updated'
        );
		
        return $input;
    }

	/**
     *  Function to create the page with form to config plugin
     */
    function configPage(){
        ?>
        <div class="wrap">
        <h2>WP CDN</h2> 
        
        <form method="post" action="options.php" id="wpcdn_form" >
         <?php
            settings_fields('wpcdn_settings');
            $this -> options = get_option('wpcdn_options');
            if (!is_array($this->options))
                $this -> options = array();
         ?>
                    
            <div id="poststuff" class="ui-sortable">
            <p>To enable wich content will be retrieved from CDN, check the following options</p>
            <div class="postbox">
                <h3 class="hndle"><span>Enable/Disable Content from CDN</span></h3>
                <div class="inside">                
                    
                    
                    
                    <div class="chkbox"><label for="images">
                        <input type="checkbox" id="images" name="wpcdn_options[]" value="images"<?php if (in_array('images', $this->options)) echo ' checked="checked"' ?> />Images</label>
                    </div>
                    <div class="chkbox"><label for="content">
                        <input type="checkbox" id="content" name="wpcdn_options[]" value="content"<?php if (in_array('content', $this->options)) echo ' checked="checked"' ?> />JS and CSS files</label>
                    </div>
                    <div class="chkbox"><label for="media">
                        <input type="checkbox" id="media" name="wpcdn_options[]" value="media"<?php if (in_array('media', $this->options)) echo ' checked="checked"' ?> />Media (avi,wmv,mpg,wav and mp3 files)</label>
                    </div>  
                    <div class="chkbox"><label for="docs">
                        <input type="checkbox" id="docs" name="wpcdn_options[]" value="docs"<?php if (in_array('docs', $this->options)) echo ' checked="checked"' ?> />Documents (txt, rtf, doc, xls, rar, zip, tar, gz and exe)</label>
                        
                    </div>
                    
                </div>
            </div>
            <div class="submit"><input type="submit" name="prof_options" id="prof_options" value="Update Options" /></div>
            </div>
        </form>
    </div>
        <?php
    }

    /**
    * Start the buffer to get the content
    */
    function pre_content()
    {
        ob_start();
		
    }
    
    /**
    * Get the content from the buffer and parse it
    */
    function post_content()
    { 
        $html = ob_get_contents();
        ob_end_clean();

        echo $this->parse($html);
    }
    
    /**
    * @param string $html
    * Parse the original host into CDN host
    */
    function parse($html)
    {
    	$this -> options = get_option('wpcdn_options');
        if ( $this -> cdn != $this -> host && is_array($this->options) && !empty($this->options)){
            //Images, except galleries
            if (in_array('images', $this->options))
            	$regex['img']  = '/' . preg_quote($this->host , '/') . '\/(((?!gallery\.php)(?![\w-]+\/gallery-image))\S+\.(png|jpg|jpeg|gif|ico))/i';
            
            //CSS and JS
            if (in_array('content', $this->options))
            	$regex['content'] = '/' . preg_quote($this->host , '/') . '\/(\S+\.(css|js))/i';
            
            //Media
            if (in_array('media', $this->options))
            	$regex['media'] = '/' . preg_quote($this->host , '/') . '\/(\S+\.(avi|wmv|mpg|wav|mp3))/i';
            
            //Documents
            if (in_array('docs', $this->options))
            	$regex['docs'] = '/' . preg_quote($this->host , '/') . '\/(\S+\.(txt|rtf|doc|xls|rar|zip|tar|gz|exe))/i';

            $html = preg_replace( $regex , $this->cdn.'/$1' , $html);
        }    
        return $html;        
    }
}