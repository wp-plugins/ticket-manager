<?php
/*
  Plugin Name: Ticket Manager
  Plugin URI:  http://easilycreate.com/MotherNature/ticket-manager.zip
  Description: The plugin helps you compare tickets found on sites like Stubhub, Ebay, Ticket Network, Tickets Now, and Vivid Seats in order to find the best prices and deals from the secondary ticket market. Our search bar is easy to use, and has a user-friendly interface, designed to get you the best ticket deals on the web. You'll find hundreds of tickets for sale in no time using it.
  Version: 1
  Author: Brizgo Technology Solutions
 */
?>
<?php
add_action('init', 'load_jquery');
add_action('init', 'load_fancybox');
add_action('init', 'load_css');
add_action('init', 'style_css');
wp_enqueue_script('jquery');

function load_jquery() {
    wp_enqueue_script( 'jquery' );
}

function load_fancybox() {
    wp_enqueue_script('ava-fancy-js', plugins_url() . '/ticket-manager/js/jquery.prettyPhoto.js', __FILE__);
}

function load_css() {
    wp_enqueue_style('ava-fancy-css', plugins_url() . '/ticket-manager/css/prettyPhoto.css', __FILE__);
}

function style_css() {
    wp_enqueue_style('ava-style-css', plugins_url() . '/ticket-manager/css/style.css', __FILE__);
}

class TicketWidget extends WP_Widget {

    function TicketWidget() {
        parent::WP_Widget(false, $name = 'Ticket Manager');
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
		$page_result = apply_filters('widget_page', $instance['page']);
        ?>

        <?php
        //ss
        echo $before_widget;
        ?>

        <?php
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){

                jQuery('#txtInput')
                .keyup(function(e) {
					jQuery('#btnEnter').hide();
                    jQuery('#txtInput').css({'background-image':'url("<?php echo plugins_url(); ?>/ticket-manager/images/loading.gif")','backgroundPosition' : 'center right','backgroundRepeat': 'no-repeat'})
        			
                    that = jQuery(this);
                    if (that.val() == "") {
                        jQuery('#txtInput').css({'background-image':'url("")'});
                        jQuery('#results').hide();
                    }else {
                        var seen = {};
                        jQuery.ajax({
                            url: 'http://api.seatgeek.com/2/events?aid=829',
                            data: {
                                q:    that.val(),
                                sort: 'score.desc',
                                per_page: 5,                                
                            },
                            type: 'GET',
                            dataType: 'jsonp',
                            success: function(resp) {
								jQuery('#btnEnter').show();
                                jQuery('#txtInput').css({'background-image':'url("")'})
                                jQuery('#results').empty();				 
                                jQuery('#results').show();
                                jQuery.each(resp.events, function(i, event) {                                    
                                    var clss='';
                                    var id='';                                    
                                    var txt = event.performers[0].name;
                                    jQuery('#hidName').val(txt);
                                    if (seen[txt])
                                        jQuery('.evntdata').remove();
                                    else
                                        seen[txt] = true;
                                    var el = jQuery('<div class="evntdata">');
                                    jQuery('<a>')					  
                                    .attr('onClick', 'listTickets(this.id)')
                                    .attr('id', event.performers[0]['slug']+"-tickets")
                                    .html(txt).appendTo(el);                                    
                                    el.appendTo('#results');                                    
                                });
        					
                            }
                        });
                        jQuery('#results').hide();
                    }
                })
        	  
                jQuery(".tbutton1").prettyPhoto();
                jQuery(".tbutton1").live('click',function(){
                      
                    var urls=this.id;
                    var title = this.title;
                    jQuery.prettyPhoto.open(urls+'?iframe=true&amp;width=100%&amp;height=100%');
                });
				
				jQuery('#target').submit(function() { 
				var ser=jQuery('#txtInput').val();
				var serch=ser.split(' ').join('-');				
				<?php $permalink = get_permalink( $page_result ); ?>
				document.location.href= '<?php echo $permalink; ?>?&search_ticket='+ser;
				return false;
				 });
            })
			function listTickets(id)
			{
				jQuery('#txtInput').val(id);
				jQuery('#results').hide();				
			}
           function listTickets(id){
				
				if(id!='')	
				{															
				   <?php $permalink = get_permalink( $page_result ); ?>
					document.location.href= '<?php echo $permalink; ?>?&search_ticket='+id;
					var href1='#';
					var rel1='prettyPhoto[iframe]';
					var class1='tbutton1';					
       			}
				else
				return false;
             }
			 
			 function getTickets(){
				var ser=jQuery('#txtInput').val();
				var serch=ser.split(' ').join('-');				
				<?php $permalink = get_permalink( $page_result ); ?>
				document.location.href= '<?php echo $permalink; ?>?&search_ticket='+ser;
				return false;
			 }
        
        </script>

        <div class="my_textbox">
			<form name="myform" id="target" method="get" action="">
				<input type='text' name='my_text' id='txtInput' class='txtInput' onclick="this.value='';" autocomplete=off />	
				<input type="hidden" value="" id="hidName"   />
			</form>
        </div>

        <div id="results" class="res" style="display: none;"></div>     

        <?php
        echo $after_widget;
        ?>
        <?php
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
		$page = esc_attr($instance['page']);        
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </label>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('page'); ?>"><?php _e('Select page for result:'); ?>
			<select id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>"  style="height: 25px; width: 145px;">
			<option value="<?php echo $page; ?>" ><?php echo get_the_title($page); ?> </option>
			<?php 
			 $pages = get_pages(); 
			 foreach ( $pages as $page ) {
					 $option = '<option value="' .  $page->ID . '">';
					 $option .= $page->post_title;
					$option .= '</option>';
					 echo $option;
			 }
			 ?>
			</select>
			
		</p>
      
        <?php
    }

}

add_action('widgets_init', 'TicketWidgetInit');

function TicketWidgetInit() {
    register_widget('TicketWidget');
}

function append_tickets_results($content) {        
        $new_content = '<div id="resultEvents"></div>';
        $content = $content . $new_content;
        return $content;
    }
function remove_page_title($title){
    //Return new title if called inside loop
    if ( in_the_loop() )
        return '';
    //Else return regular   
    return $title;
}
function ticket_listing(){
	global $wp_query;
	$id = $wp_query->post->ID;
    $search_id = $_GET['search_ticket'];
	if($search_id!= ''){
add_filter('the_title', 'remove_page_title');
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                showTicketS_2('<?php echo $search_id ;?>');
                
                 jQuery(".tbutton1").prettyPhoto();
                 jQuery(".tbutton1").live('click',function(){                      
                    var urls=this.id;
                    var title = this.title;
                    jQuery.prettyPhoto.open(urls+'?iframe=true&amp;width=100%&amp;height=100%');
                    
                });                             
               
            });
            function showTicketS_2(id){
            
                var href1='#';
                var rel1='prettyPhoto[iframe]';
                var class1='tbutton1';      
				var text = jQuery('#'+id).text();
				var repName = id;
				var name= repName.replace(/-/g, " "); 

            jQuery.ajax({
                url: 'http://api.seatgeek.com/2/events?aid=829',
        				
                data: {
                    q:  id,
                    sort: 'datetime_utc.asc',
                    per_page: 100000,
                          
                },
                type: 'GET',
                dataType: 'jsonp',
                success: function(resp) {
                  
                    var total=resp.meta['total'];
                    jQuery('#resultEvents').html('<div class="titles" align="left"><h1>'+ucwords(name)+'</h1><span class="ecount"><span class="tCount">'+total+ '</span> events found.</span><div class="bluebar"></div> <input type="hidden" id="current_page" /> <input type="hidden" id="show_per_page" /></div>');							  
                    var mainDiv = jQuery('<div>').attr('id','main_div')
                                                    
                    var res =   mainDiv.html();
                    
                    mainDiv.appendTo('#resultEvents');	                                    
        					
                    jQuery.each(resp.events, function(i, event) {					
                        var avgPrice;
						var lowPrice;
						var highPrice;
                        var edate=event.datetime_local;
                        var fields = edate.split(/T/);
                        var tdate = fields[0];
                        var ttime = fields[1];
                        var hh = ttime.split(/:/);
                        var h = hh[0];
                        var m = hh[1];
                        //alert(h);
                        var ampm;
                        if(h >= 12){
                            var h1=h-12;
                            ampm= 'PM';
                        }
                        else if(h < 12){
                            h1=h;
                            m=m;
                            ampm= 'AM';
                        }
                        if (h == 0) {
                            h1 = 12;
                            ampm= 'AM';
                        }
						if(event.stats['average_price']==null || event.stats['average_price']=='')
						{
							avgPrice='N/A';
						}
						else
						{
							avgPrice = '$'+event.stats['average_price'];
						}
						if(event.stats['lowest_price']==null || event.stats['lowest_price']=='')
						{
							lowPrice='N/A';
						}
						else
						{
							lowPrice = '$'+event.stats['lowest_price'];
						}	
						if(event.stats['highest_price']==null || event.stats['highest_price']=='')
						{
							highPrice='N/A';
						}
						else
						{
							highPrice = '$'+event.stats['highest_price'];
						}				
								
                        var el = jQuery('<span>');
                        jQuery('<a>')
                        .attr('href',href1)						  
                        .attr('rel',rel1)
                        .attr('class',class1)
                        .attr('id',event.url) 
                        .html('<div class="listing" id="listing" align="left" ><div class="date" align="center"><br />'+tdate+'<br />'+h1+':'+m+':'+ampm+'</div><div class="heads"><span class=event-name">' + event.title + ' </span><br /> <span class="performer">'+event.performers[0]['name']+'  ' + event.venue['city']+', '+ event.venue['state'] +'</span><br/><span class="price"><span class="avg_price">Avg Price: <span class="bold">'+avgPrice+'</span></span> <span class="avg_price">Lowest Price: <span class="bold">'+lowPrice+'</span></span><span class="avg_price">Highest Price: <span class="bold">'+highPrice+'</span></span></span></div><div class="tbutton" id="'+event.url+'" title="'+event.title+'"><img src="<?php echo plugins_url(); ?>/ticket-manager/images/button_ticket.png" />  </div> </div>').appendTo(el);
                        el.appendTo('#main_div');
                    });  
                    var $pagination = jQuery('<div>')
                    .attr('id','page_navigation_inbox')						  
                    $pagination.appendTo('#resultEvents');	  
        					     
                    var show_per_page = 25;  
                    var number_of_items = jQuery('#main_div').children().size(); 					
                    var number_of_pages = Math.ceil(number_of_items/show_per_page);  
                    jQuery('#current_page').val(0);  
                    jQuery('#show_per_page').val(show_per_page);    
        	  
                    var navigation_html = '<a class="previous_link" id ="previous_link" href="javascript:previous();"></a>';  
                    var current_link = 0;  
        	
                    while(number_of_pages > current_link){  	
                        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>' ;  
                        current_link++;  
                    }  
                    navigation_html += '<a class="next_link" id="next_link" href="javascript:next();"></a>';
          
                    jQuery('#page_navigation_inbox').html(navigation_html);  
					var show_per_page = parseInt(jQuery('#show_per_page').val());   
					start_from = 0 * show_per_page;      
					end_on = start_from + show_per_page;   
					jQuery('#main_div').children().css('display', 'none').slice(start_from, end_on).css('display', 'block'); 
					jQuery('.page_link[longdesc=' + 0 +']').addClass('active_page').siblings('.active_page').removeClass('active_page');    
					//update the current page input field  
					jQuery('#current_page').val(0);    
            
                    
                }
        		 
                      
            }) 
        }

        function previous(){   
            new_page = parseInt(jQuery('#current_page').val()) - 1;     
            if(jQuery('.active_page').prev('.page_link').length==true){  
                go_to_page(new_page);  
            }  
        }  
          
        function next(){  
            new_page = parseInt(jQuery('#current_page').val()) + 1;      
            if(jQuery('.active_page').next('.page_link').length==true){  
                go_to_page(new_page);  
            }   
        		
        }  
        function go_to_page(page_num){    
            var show_per_page = parseInt(jQuery('#show_per_page').val());   
            start_from = page_num * show_per_page;      
            end_on = start_from + show_per_page;   
            jQuery('#main_div').children().css('display', 'none').slice(start_from, end_on).css('display', 'block'); 
            jQuery('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');    
            //update the current page input field  
            jQuery('#current_page').val(page_num);  
        } 
		function ucwords (str) { 
		  return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
			return $1.toUpperCase();
		  });
}

        </script>
        <?php
	}
}
add_action('wp_head', 'ticket_listing');
add_filter('the_content', 'append_tickets_results');

?>