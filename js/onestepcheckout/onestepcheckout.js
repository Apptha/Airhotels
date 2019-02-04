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
 * @package     Apptha_Onestepcheckout
 * @version     0.1.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */

var Checkout = Class.create();
Checkout.prototype = {
    initialize: function (urls) {

        this.reviewUrl = urls.review;
        this.saveMethodUrl = urls.saveMethod;
        this.failureUrl = urls.failure;
        this.billingForm = false;
        this.shippingForm = false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';
        this.loadWaiting = false;
        this.steps = ['login', 'billing', 'shipping', 'shipping_method', 'payment', 'review'];

        labels = document.getElementsByTagName("button"); 
        for (var i = 0; i < labels.length; i++ )
        {
        if( labels[i].className == 'button btn-proceed-checkout btn-checkout' ) 
        //labels[i].style.display="none";
        labels[i].onclick = showCheckout;
        } 
        
        function showCheckout() {
        	//$.scrollTo( '#scroll', 800, {easing:'elasout'} );
        	ScrollToControl('scroll');
        	//document.getElementById('scroll').scrollIntoView();
        	//document.getElementById('scroll').animate({ duration: 'slow'});

        }

    },

    ajaxFailure: function(){

    
    location.href = this.failureUrl;
    },
    loadingbox: function ()
    {

        $("checkout-review-load").update('<div class="loadingWrapper"><div class="content three-bounce"><div class="one"></div><div class="two"></div><div class="three"></div></div></div>')
    }, 
    //update the product information
    reloadReviewBlock: function(){
        var updater = new Ajax.Updater('checkout-review-load', this.reviewUrl, {
            method: 'get',
            onLoading:this.loadingbox.bind(this),
            onFailure: this.ajaxFailure.bind(this)
            });
        
    }
}

//payment function starts
var Payment = Class.create();
Payment.prototype = {

    initialize: function(reviewUrl,paymentUrl){

        this.reviewUrl = reviewUrl;
        this.paymentUrl = paymentUrl;

    },
    ajaxFailure: function(){
    // $("checkout-review-load").update('<div class="loading-ajax">&nbsp;</div>')
    },
      //onloading of  product information save
    loadingbox: function () {
        $("checkout-review-load").update('<div class="loadingWrapper"><div class="content three-bounce"><div class="one"></div><div class="two"></div><div class="three"></div></div></div>')
    },
     //after product information save sucessfully
    ajaxSucess: function(){
        Element.hide('payment-please-wait');
    },

reloadReviewBlock: function(step){      
    	 var request = new Ajax.Request(
         		this.reviewUrl,
             {
                 method:'post',
                 parameters: Form.serialize('co-form'),
                 onLoading:this.loadingbox.bind(this),
                 onComplete: this.onComplete,
                 onSuccess: function(transport)    {
                     if(transport.status == 200)    {
                    	 $("checkout-review-load").update(transport.responseText)
                     }
                 },
             onFailure: this.ajaxFailure.bind(this)
         }
         );
    },
 reloadPaymentBlock: function(step){
        
        var updater = new Ajax.Updater('ajax-payment-methods', this.paymentUrl, {
            method: 'get',
            onLoading:this.loadingbox.bind(this),
            onFailure: this.ajaxFailure.bind(this),
            onSuccess: function()    {
            	//$('payment_form_ccsave').show();  
            	
            },
            onComplete: function() {
            	//alert(paymentMethod);
                payment.switchMethod(paymentMethod);
            }
            
            });
    },
     //function triggers when payment methods change
    switchMethod: function(method){
    	if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
        }
        if ($('payment_form_'+method)){
                this.changeVisible(method, false);
            $('payment_form_'+method).fire('payment-method:switched', {
                method_code : method
            });
        } else {
        //Event fix for payment methods without form like "Check / Money order"
        //document.body.fire('payment-method:switched', {method_code : method});
        }
        this.currentMethod = method;
        this.reloadReviewBlock('payment');
        
        
    },

    changeVisible: function(method, mode) {
        var block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].each(function(el) {
            element = $(el);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                element.select('input', 'select', 'textarea').each(function(field) {
                    field.disabled = mode;
                });
            }
        });
    }

}
var Billing = Class.create();
Billing.prototype = {
    initialize: function(form, addressUrl, saveUrl){
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);

    },
//set the billing address
    setAddress: function(addressId){
        if (addressId) {
            request = new Ajax.Request(
                this.addressUrl+addressId,
                {
                    method:'get',
                    onSuccess: this.onAddressLoad,
                    onFailure: checkout.ajaxFailure.bind(checkout)
                    }
                );
        }
        else {
            this.fillForm(false);
        }
    },
//triggers when change the billing address to new address
    newAddress: function(isNew){
        if (isNew) {
            //this.resetSelectedAddress();
            Element.show('billing-new-address-form');
       } else {
            Element.hide('billing-new-address-form');
        }
    },
//triggers when change the billing address
    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                elementValues = {};
            }
        }
        else{
            //this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^billing:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm){
                    billingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },

    setUseForShipping: function(flag) {
        $('shipping:same_as_billing').checked = flag;
    }



}
//review function starts 
var Review = Class.create();
Review.prototype = {
    initialize: function(form,saveUrl,successUrl,agreementsForm){
        this.form = form;
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;

        this.agreementsForm = agreementsForm;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
      //function triggers when onloading on review save function
    loadingbox: function () {
         var translate = Translator.translate('processing').stripTags();
        $("review-please").update(' <div class="please-wait-loading">&nbsp;</div><span class="load-wait">'+translate+'</span>')
    var form = $('review-btn');
    form.disabled='true';
  
    },
      
    save: function(){
 var paymentmethod = payment.currentMethod;
        var validator = new Validation(this.form);
        if (validator.validate()) {


            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    parameters: Form.serialize(this.form),
                    onLoading:this.loadingbox.bind(this),
                    onComplete: this.onComplete,
                    onSuccess: function(transport)    {
                        if(transport.status == 200)    {
                            var data = transport.responseText.evalJSON();
                            if(!data.success)
                            {
                                alert(data.error_messages);
                                $("review-please").update('');
                                 $('review-btn').disabled='';

                            }
                            if (data.redirect) {
                                location.href = data.redirect;
                                return;
                            }
                            if(data.success){
                               //hostedpro and advanced payment action
                                if(paymentmethod == 'hosted_pro' || paymentmethod =='payflow_advanced')
                                {
                                        Element.hide('review-please');
                                        Element.hide('review-btn');
                                        document.getElementById('checkout-paypaliframe-load').style.display= 'block';
                                        iframedata = data.update_section["html"].replace("display:none","display:block");
                                        document.getElementById('checkout-paypaliframe-load').innerHTML = iframedata;

                                }
                                else   //other payment action
                                {
                                    this.isSuccess = true;
                                    window.location = data.success;
                                }
                            }
                        }
                    },
                onFailure: checkout.ajaxFailure.bind(checkout)
            }
            );
    //var updater = new Ajax.Updater('product-details', this.saveUrl, {method: 'post',parameters: Form.serialize(this.form)});
    }
},

resetLoadWaiting: function(transport){
    //  checkout.setLoadWaiting(false, this.isSuccess);
    },

    nextStep: function(transport){
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
            if (response.redirect) {
                location.href = response.redirect;
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                window.location=this.successUrl;
            }
            else{
                var msg = response.error_messages;
                if (typeof(msg)=='object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg);
                }
            }

            if (response.update_section) {
                $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section);
                checkout.reloadProgressBlock();
            }
        }
    },

    isSuccess: false
}
var Shipping = Class.create();
Shipping.prototype = {
    initialize: function(form, addressUrl,methodsUrl,reloadUrl){
        this.form = form;

        this.addressUrl = addressUrl;
        this.reloadUrl = reloadUrl;
        this.methodsUrl = methodsUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);

    },

    setAddress: function(addressId){
        if (addressId) {
            request = new Ajax.Request(
                this.addressUrl+addressId,
                {
                    method:'get',
                    onSuccess: this.onAddressLoad,
                    onFailure: checkout.ajaxFailure.bind(checkout)
                    }
                );
        }
        else {
            this.fillForm(false);
        }
    },
//triggers when change the shipping address to new address
    newAddress: function(isNew){

        if (isNew) {
          //this.resetSelectedAddress();
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
        }
    //shipping.setSameAsBilling(false);
    },
//triggers when change the shipping address
    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },
 
    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                elementValues = {};
            }
        }
        else{
            //this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^shipping:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm){
                    shippingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
//function to set shipping same as billing 
    setSameAsBilling: function(flag) {
        var value;
        var address;
        var select_address;
        $('shipping:same_as_billing').checked = flag;
        value = $('shipping:address_id').value
        address = $('shipping:has_addresss').value
        if ($('shipping-address-select') == undefined) { 
            if (flag)
            {
                Element.hide('shipping-new-address-form');
            }
            else
            {
                Element.show('shipping-new-address-form');
            }
            select_address = -1;
            return false;
        }
        else
        {
            select_address = $('shipping-address-select').value;
        }
      if (flag)
{
            if((value)&&(address!=0))
            {  
                Element.hide('shipping-new-address-form');
                Element.hide('shipping-old-address-form');
            }
           
            else if(value)
            { 
                Element.hide('shipping-new-address-form');
            //Element.hide('shipping-old-address-form');
            }
            else
            { 
                Element.hide('shipping-new-address-form');
                Element.hide('shipping-old-address-form');
            }
        }
        else
        {
          
            if((value)&&(address!=0) && select_address !='')
            {
                Element.show('shipping-old-address-form');
            }
            else if((value) && select_address =='')
            {
                Element.show('shipping-old-address-form');
                Element.show('shipping-new-address-form');
               
            }
            else if((!value) && select_address =='')
            {
                Element.show('shipping-old-address-form');
                Element.show('shipping-new-address-form');

            }
           
            else if(value)
            {

                Element.show('shipping-old-address-form');
            }
            else
            {
                Element.show('shipping-new-address-form');
            }
        }


    },

    syncWithBilling: function () {

    },
    loadingbox: function () {
        $("checkout-review-load").update('<div class="loading-ajax">&nbsp;</div>')
    },
    //load the product information
    reloadReviewBlock: function(){
        var updater = new Ajax.Updater('checkout-review-load', this.reloadUrl, {
            method: 'post',
            onLoading:this.loadingbox.bind(this),
            parameters:Form.serialize(this.form)
            });
    },
    //set shipping region to billing region
    setRegionValue: function(){
        $('shipping:region').value = $('billing:region').value;
    }


}

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function(form){
        this.form = form;

    }
}
function switchMethod2()
{ 
    var  selectregisterElement= $('register_customer');

    if ($('login:guest').checked)
    {
        Element.hide('register-customer-password');
        Element.hide('register-customer-confirmpassword');
        selectregisterElement.value='';
    }
    else
    {
        Element.show('register-customer-password');
        Element.show('register-customer-confirmpassword');
        selectregisterElement.value='register';
    }
}
window.onload = function() {
	for (var i = 0, l = document.getElementsByTagName('input').length; i < l; i++) {
		if(document.getElementsByTagName('input').item(i).type == 'text') {
			document.getElementsByTagName('input').item(i).setAttribute('autocomplete', 'off');
		};
	};
};



//Scroll smooth effect for proceed to checkout

function elementPosition(obj) {
	var curleft = 0, curtop = 0;

	if (obj.offsetParent) {
	curleft = obj.offsetLeft;
	curtop = obj.offsetTop;

	while (obj = obj.offsetParent) {
	curleft += obj.offsetLeft;
	curtop += obj.offsetTop;
	}
	}

	return {x: curleft, y: curtop};
	}

	function ScrollToControl(id)
	{
	var elem = document.getElementById(id);
	var scrollPos = elementPosition(elem).y;
	scrollPos = scrollPos - document.documentElement.scrollTop;
	var remainder = scrollPos % 50;
	var repeatTimes = (scrollPos - remainder) / 50;
	ScrollSmoothly(scrollPos,repeatTimes);
	window.scrollBy(0,remainder);
	}
	var repeatCount = 0;
	var cTimeout;
	var timeoutIntervals = new Array();
	var timeoutIntervalSpeed;
	function ScrollSmoothly(scrollPos,repeatTimes)
	{
	if(repeatCount < repeatTimes)
	{
	window.scrollBy(0,50);
	}
	else
	{
	repeatCount = 0;
	clearTimeout(cTimeout);
	return;
	}
	repeatCount++;
	cTimeout = setTimeout("ScrollSmoothly('" + scrollPos + "','"+ repeatTimes +"')",110);
	}


