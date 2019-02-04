/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/*global window */
/* added the social login links into default login links */
document.observe("dom:loaded", function() {
    var i;
    try {
    	var elements = document.getElementsByClassName("wish_list_button");
        for (i = 0; i < elements.length; i++) {
        	var url = elements[i].href;
        	var id = url.substring(url.lastIndexOf("product")+8,url.lastIndexOf("/form_key"));        
        	url = JSON.stringify(url);
        	elements[i].href = 'javascript:ajaxnewwishlist('+url+','+id+')';        		
        }
    }
    catch (exception) {
        alert(exception);
    }
});
function ajaxnewwishlist(url,id){
	if(jQuery( ".wishlist-icons_"+id ).hasClass( "pink-icon" ) ) {  
		removeWishlist(url,id);
	}else{
		ajaxWishlist(url,id);
	}
}
// Ajax Compare starts here
function ajaxWishlist(url,id){	
	jQuery('#whislist_message').replaceWith('<div id="whislist_message"></div>');
    url = url.replace("wishlist/index","airhotels/multistep");
    url += 'isAjax/1/';    
    jQuery('#ajax_loading'+id).show();
    jQuery.ajax( {
        url : url,
        dataType : 'json',
        success : function(data) {
            jQuery('#ajax_loading'+id).hide();
            if(data.status == 'ERROR'){                
                jQuery('#whislist_message').replaceWith('<div id="whislist_message" style="text-align: left;color: red;font-size: 16px;margin-top: 18px;">'+data.message+'</div>');
            	jQuery("#whislist_message").fadeOut(3500);
            }else if(data.status == 'NOTLOGGED'){
            	javascript:apptha_sociallogin();
            }else{                
                if(data.status == 'SUCCESS'){    
                	jQuery('#whislist_message').replaceWith('<div id="whislist_message" style="text-align: left;color: green;font-size: 16px;margin-top: 18px;">'+data.message+'</div>');
                	jQuery("#whislist_message").fadeOut(3500);
                	jQuery(".wishlist-icons_"+id).removeClass("icon");
                	jQuery(".wishlist-icons_"+id).addClass("pink-icon");
                }
                if(jQuery('.block-wishlist').length){
                    jQuery('.block-wishlist').replaceWith(data.sidebar);
                }else{
                    if(jQuery('.col-right').length){
                        jQuery('.col-right').prepend(data.sidebar);
                    }
                }
            }
        }
    });
}
function removeWishlist(url,id){ 	
	jQuery('#whislist_message').replaceWith('<div id="whislist_message"></div>');
	url = url.replace("wishlist/index/add","airhotels/multistep/removewishlist");
	url += 'isAjax/1/id/'+id;
	jQuery.ajax( {
		url : url,
		dataType : 'json',
		success : function(data) {
			if(data.status == 'ERROR'){
				alert(data.message);
			}else{	
				 if(data.status == 'SUCCESS'){    
					jQuery('#whislist_message').replaceWith('<div id="whislist_message" style="text-align: left;color: green;font-size: 16px;margin-top: 18px;">'+data.message+'</div>');
					jQuery("#whislist_message").fadeOut(3500);
                	jQuery(".wishlist-icons_"+id).removeClass("pink-icon");
                	jQuery(".wishlist-icons_"+id).addClass("icon");                	
                }
				if(jQuery('.block-compare').length){
                    jQuery('.block-compare').replaceWith(data.sidebar);
                }else{
                    if(jQuery('.col-right').length){
                    	jQuery('.col-right').prepend(data.sidebar);
                    }
                }
			}
		}
	});
}