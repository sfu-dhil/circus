

jQuery(document).ready(function($){
    /* Initialize the tooltips (which are currently only used in the search) */
    $(".search-tooltip").tooltip();
    
    
    
    /* And then if there are any inputs with values
     * in the advanced search controls, then force it open  */
    var controls = $("#controls");
    var inputs =  controls.find("input:checked, input[type='text'][value]");
    var selectedIndex = $("#clipping_search_order").prop("selectedIndex");
    if (inputs.length > 0 || selectedIndex > 0){
        controls.attr("aria-expanded", "true");
        controls.addClass("in");
    }
});