# popover_page_tour
Use bootstrap popovers to create an elaborate tour of a web page and website




## Setting Up
### Initial set up:
1. In My controller place:
```
public function _with_popover_tour($page, $auto_start = TRUE){
    $this->data['__tour_page'] = TRUE;
    $this->data['__tour_auto_start'] = $auto_start;
    $this->data['__tour_section'] = $page;
}
```
2. In your layout template place:
```
<?php if(isset($__tour_page) && $__tour_page): ?>
    <!-- For any page that needs tour functionality -->
    <?php $this->load->view('scripts/templates/popover_tour.php'); ?>
    <!-- EO basic tour functionality -->
<?php endif; ?>
```
3. Create function in controller and update location of function for ajax call contained in tourBodyUrl
```
public function ajax_get_popover_body(){
    $body = $this->load->view('templates/partials/tour_body', $this->data, TRUE);
    return $this->ajax_response($body, NULL);
    
} 
```
..* tour_body html:
```
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
```
4. Call as needed
in your controller method, when you want to call this simple call:
```
$this->_with_popover_tour('page', NULL);
param 1 = page for the tour, needs to reflect a propert in tourSections below
param 2= autoStart tour, by default tour will auto start
//set up json object below with tour items
```

