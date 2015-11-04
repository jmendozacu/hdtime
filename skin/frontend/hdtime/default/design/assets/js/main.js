(function ($) {

  // iOS orientation
  $(document).ready(function() {
    if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) {
      var viewportmeta = document.querySelector('meta[name="viewport"]');
      if (viewportmeta) {
        viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
        document.body.addEventListener('gesturestart', function () {
          viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=1.6';
        }, false);
      }
    }
  });

})($j);


(function ($) {
  $(document).ready(function() {
    
    //-------------------------------------------------------------
    // Jcarousel on tabs
    //-------------------------------------------------------------
    $('#tab-prod-new').on('click', '.nav-tabs', function() {      
      $('#carousel-new').jcarousel('reload', 0);
    });
	$('#prod-together').on('click', '.nav-tabs', function() {      
      $('#carousel-new2').jcarousel('reload', 0);
    });
	$('#prod-pross').on('click', '.nav-tabs', function() {      
      $('#carousel-new3').jcarousel('reload', 0);
    });
    $('#tab-prod-sales').on('click', '.nav-tabs', function() {
      $('#carousel-sales').jcarousel('reload', 0);
    });   
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      e.target // activated tab
      e.relatedTarget // previous tab
      $('#carousel-new').jcarousel('reload', 0);
	  $('#carousel-new2').jcarousel('reload', 0);
	  $('#carousel-new3').jcarousel('reload', 0);
      $('#carousel-sales').jcarousel('reload', 0);
    })
    // end of Jcarousel on tabs
    
    //-------------------------------------------------------------
    // Slider range in forms
    //-------------------------------------------------------------

      /*
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 50000,
      values: [ 3500, 23990 ],
      slide: function( event, ui ) {        
        document.getElementById('range-min').innerHTML = "от " + ui.values[ 0 ] + " <span class=\"rub\">i</span>";
        document.getElementById('range-max').innerHTML = "до " + ui.values[ 1 ] + " <span class=\"rub\">i</span>";
      }
    });
    */

    // end of Slider range in forms

     /*
    $(".pop-over").popover({
      html: true
    });
    */

    $('#footer-menu').affix({
      offset: {
        top: 70,
        bottom: 0
      }
    });

    //Sho/less options on product page
     /* $j(".custom-radio-configurable").each(function(){
          $j(this).find('.show-more').click(function () {
              $j(this).find('.show-more').hide();
              $j(this).find('.show-less').show();
              $j(this).find('.radio-item').show();
          }.bind(this));
          $j(this).find('.show-less').click(function () {
              $j(this).find('.show-less').hide();
              $j(this).find('.show-more').show();
              $j(this).find('.radio-item:has(label):gt(4)').hide()
          }.bind(this));
      });*/


    
  }); //end of $(document).ready()

    function setEqualHeight(columns) {
        var tallestcolumn = 0;
        columns.each(
            function() {
                currentHeight = $(this).height();
                if(currentHeight > tallestcolumn) {
                    tallestcolumn = currentHeight;
                }
            }
        );
        columns.height(tallestcolumn);
        $("#1main-tab-content").height(tallestcolumn - 55);
    }

    $(document).ready(function() {
        setEqualHeight($(".same-height > div"));
    });

})($j);

(function ($) {
  $(document).ready(function(a) {
    
    //-------------------------------------------------------------
    // Count form with +/- buttons
    //-------------------------------------------------------------
      
      /*a('body').on('click', '.count>button', function () {
        var data = a(this).siblings('input'),
          val = +data.val();
        data.val(val += this.className == 'minus' ? val>1 ? -1: 0: 1);
      });*/
 
    // end of Count form with +/- buttons
    
  }); //end of $(document).ready()
})(window.jQuery);
Equals = function(block){
    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;

    block.each(function() {

        $el = jQuery(this);
        topPostion = $el.position().top;

        if (currentRowStart != topPostion) {

            // we just came to a new row.  Set all the heights on the completed row
            for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }

            // set the variables for the new row
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);

        } else {

            // another div on the current row.  Add it to the list and check if it's taller
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);

        }

        // do the last row
        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }

    });
} 
