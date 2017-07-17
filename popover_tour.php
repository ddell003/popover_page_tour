<?php 


/*----------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------Setting Up----------------------------------------------*/
/*----------------------------------------------------------------------------------------------------------*/
/*

//initial set up:

1.In My controller place:

public function _with_popover_tour($page, $auto_start = TRUE){

    $this->data['__tour_page'] = TRUE;
    $this->data['__tour_auto_start'] = $auto_start;
    $this->data['__tour_section'] = $page;
}

2.in your layout template place:
<?php if(isset($__tour_page) && $__tour_page): ?>
    <!-- For any page that needs tour functionality -->
    <?php $this->load->view('scripts/templates/popover_tour.php'); ?>
    <!-- EO basic tour functionality -->
<?php endif; ?>

3.create function in controller and update location of function for ajax call contained in tourBodyUrl

public function ajax_get_popover_body(){

    $body = $this->load->view('templates/partials/tour_body', $this->data, TRUE);

    return $this->ajax_response($body, NULL);
    
} 
tour_body html:
<div class="popover tour-tour tour-tour-3 fade bottom in" role="tooltip" id="step-3" style="display: block;"> 

    <div class="arrow" style="left: 50%;"></div> 

    <h3 class="popover-title">Title of my step</h3> 

    <div class="popover-content">
        
        Introduce new users to your product by walking them through it step by step.

    </div> 

    <div class="popover-navigation"> 

        <div class="btn-group"> 

            <a class="btn btn-sm btn-info prev_item" data-role="prev">« Prev</a> 
            <a class="btn btn-sm btn-primary next_item" data-role="next">Next »</a>  

        </div> 

        <a class="btn btn-sm btn-danger end_tour" data-role="end">End tour</a> 

    </div> 

</div>

//call as needed
in your controller method, when you want to call this simple call:
$this->_with_popover_tour('page', NULL);
param 1 = page for the tour, needs to reflect a propert in tourSections below
param 2= autoStart tour, by default tour will auto start

//set up json object below with tour items

*/

?>

<script>

$(document).ready(function() {

    /*----------------------------------------------------------------------------------------------------------*/
    /*-------------------------------------------------Start Config----------------------------------------------*/
    /*----------------------------------------------------------------------------------------------------------*/
	console.log('scripts/templates/popover_tour');

    //load page this is being ran for
    var page = "<?php echo $__tour_section; ?>";

    //default to auto fire off
    var autoStart = "<?php echo $__tour_auto_start; ?>";
    var tourBodyUrl = "<?php echo base_url('index.php/demo/ajax_get_popover_body'); ?>";

	//New tour sections: create a new property and add it to tour sections
    //Expand tour sectrion: create a new object and add it the the array of objects for said property

    //link = item you want the popover to attach too
    //title = "popover-title"
    //message = "popover-content"

    //object of arrays with objects
	var  tourSections = {

        //for demo page
        demo: [
    	
    		{

    			'link':'.name_dropdown',
    			'message':'This is the name drop down, from here you can view personal information and change user roles, as well as logging out',
    			'title':'Name Drop Down'
    		},
    		{

    			'link':'.dashboard',
    			'message':'This is the overall facility dashboard which will show you your current tasks elapsed tasks and items that need follow up',
    			'title':'Dashboard'
    		},
    		{

    			'link':'#mi_dropdown',
    			'message':'Click here to reveal additional links to ...',
    			'title':'Mechanical Integrity Drop Down'
    		},
    		{

    			'link':'.help',
    			'message':'Come here to email support and to view knowledge base',
    			'title':'Help'
    		}
    		,
    		{

    			'link':'#google_translate_element',
    			'message':'Come here to change the language',
    			'title':'Language',
    			'location':'bottom',
    		}
    		,
    		{

    			'link':'.side_collaps',
    			'message':'Come here to change the language',
    			'title':'Language',
    			'location':'right',
    		}

    					
    	]

    };

    var tour;
    //see if tour section has been added to json object
    if(tourSections[page]){
        var tour = tourSections[page];
    }
    else{
        console.log('Tour Section Not Set up for the page: '+page);
    }
    
	var tourBody = 'Not Set';
	var count = 0;
	var tourItems = tour.length;
	var defaultPosition = 'left';
	var currentElement = '';

	//wait until we get the popover body
	getTourBody(function(){

		console.log('completed getTourBody');
	});
    /*----------------------------------------------------------------------------------------------------------*/
    /*-------------------------------------------------End Config----------------------------------------------*/
    /*----------------------------------------------------------------------------------------------------------*/

    //default auto fire off tour, otherwise wait for user to select start tour or help
    if(autoStart){

        startTour();
    }
	console.log('Total tour items: '+tourItems);
	
    //start tour
	$('.startTour').on('click', function(e){


    	console.log('tour started!');
    	$(this).addClass('hidden');
        startTour();       

    });

    //get next tour item
    $(document).on('click', '.next_item', function(e){

        //hide last popover item
    	$(currentElement).popover('hide');
        //get next item
    	startTour()
    	console.log('next_item');
                
    });

    //end tour
    $(document).on('click', '.end_tour', function(e){

        //show tour start button
    	$('.startTour').removeClass('hidden');
        //hide current popover element
    	$(currentElement).popover('hide');
    	               
    });
    
    //get previous tour item
    $(document).on('click', '.prev_item', function(e){

    	$(currentElement).popover('hide');

    	//get last item
    	var prevItem = tour.pop();

    	//add it to begining
    	tour.unshift(prevItem);

    	prevItem = tour.pop();
    	tour.push(prevItem);
    	addDataPopoverElements(prevItem);
    	    	               
    });
    //ajax out to get tour body 
    function getTourBody(callback){

    	var url = tourBodyUrl; 
    	
    	$.ajax(
		{
			url:url,

    		success:function(result){

    			//decode the encode html and pass to tourBody variable declared on page load
    			tourBody = JSON.parse(result);		      
		    }
		});
    }
    
    //start tour logic
    function startTour(){


    	//get first item so we can use it, and then place it on end of array
    	var item = tour.shift();
    	tour.push(item)
    	console.log('Tour Target: '+item.link);
    	addDataPopoverElements(item);
    	

    }
    function addDataPopoverElements(item){

        //get target element we want to attach popover to
    	currentElement = item.link;

    	//check to see if target item exists in the dom
    	var exists = ($(document).hasClass(currentElement) || $(currentElement).length);

    	//if tour doesnt exist, then skip to the next item
    	if(! exists){
    		startTour();
    	}
        //get position of popover, default to be to right of target item
    	var location = (item.location) ? item.location : 'right';

        //call bootstrap popover object
    	$(item.link).popover({

            //where we are attaching popver too
            placement:location,
            //we are triggering the popover
            trigger:'manual',
            //template of popover body
            template:tourBody,
            //allows you to place html elements on popover content
            html:true,

            container:item.link,
            //content we want to pass to template
            content:item.message,
            //title we want to pass to template 
            title:item.title,

        });
		//$(item.link).append(html);
        $(item.link).popover('show');

        //custom css for popover items 
        $('.popover').css('min-width','250px');
        $('.popover').css('border-radius','6px');
        $('.popover-navigation, .popover-content, .popover').css('background-color','#a0cfee');

        $('.popover-navigation, .popover-content').css('border-radius','0');
        $('.popover-title').css('background-color','#3498DB');
        $('.popover-title, .popover-content').css('color','#fff');


        $('.next_item, .prev_item, .end_tour').css('padding','8px 8px');
        $('.next_item, .prev_item, .end_tour').css('min-height','5px');

    }

	
});
</script>
