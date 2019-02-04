var jQ = jQuery.noConflict();
var myWidth;
if( typeof( window.innerWidth ) == 'number' ) {
 //Non-IE
   myWidth = window.innerWidth;
}
jQ(document).ready(function() {
	jQ('#sidebar').stickySidebar({
		sidebarTopMargin: 20,
      	footerThreshold: 100
	});
});             
jQ(function () {
	jQ(".rslides_config").responsiveSlides({
		speed: 1000,
		nav: true,
		namespace: "callbacks",
        maxwidth: myWidth
	});    
});  
jQ(document).ready(function() {
   jQ('.fancybox-thumbs')
   .attr('rel', 'gallery')
   .fancybox({
       prevEffect : 'none',
       nextEffect : 'none',
       padding : 0,
       closeBtn  : true,
       arrows    : true,
       nextClick : false,
       prevClick : false,
       helpers : {
       title : {
         type : 'outside'
       },
       overlay : {
         showEarly : false,
       },
       thumbs : {            
         width  : 100,
         height : 67
       }
      }
});
jQ('.fancybox-thumbs_gallery')
.attr('rel', 'gallery')
.fancybox({
       prevEffect : 'none',
       nextEffect : 'none',
       padding : 0,
       closeBtn  : true,
       arrows    : true,
       nextClick : false,
       prevClick : false,
       helpers : {
       title : {
         type : 'outside'
       },
       overlay : {
         showEarly : false,
       },
       thumbs : {            
         width  : 100,
         height : 67
       }
      }
   });
});
jQ(".more_btn").click(function (e) {
	jQ(".less_btn").show();
	jQ(".more_btn").hide();
	jQ(".hostInfo").addClass("hostinfo_btn");
	jQ(".hostInfo").removeClass("hostInfo_less");
});
jQ(".less_btn").click(function (e) {
	jQ(".less_btn").hide();
	jQ(".more_btn").show();
	jQ(".hostInfo").addClass("hostInfo_less");
	jQ(".hostInfo").removeClass("hostinfo_btn");
});
// show hide option script for more and less reviews.
jQ("#morereviews").click(function (e) {
	jQ(this).hide();
	jQ("#lessreviews").show();	
});
jQ("#lessreviews").click(function (e) {
	jQ(this).hide();
	jQ("#morereviews").show();	
});
