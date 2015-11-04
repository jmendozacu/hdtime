/**
* @author Amasty Team
* @copyright Copyright
* @package Amasty_Scheckout
*/ 

var amOnepageCheckout = new Class.create();

amOnepageCheckout.prototype = {
    mainContainer: null,
    checkoutSteps: null,
    defaultCheckout: null,
    areas: {},
    fields: {},
    options: {},
    scripts: [],
    containers: {
       login: null, 
       billing: null,
       shipping: null,
       shipping_method: null,
       payment: null,
       review: null
    },
    amorderattr:{
       login: null, 
       billing: null,
       shipping: null,
       shipping_method: null,
       payment: null,
       review: null 
    },
    billingRefreshableFields: {
        id: [
            'billing:country_id'
        ],
        name: []
    },
    shippingRefreshableFields: {
        id: [
            'shipping:country_id'
        ],
        name: []
    },
    replaceClassesSets: function(container, classes){
        
        for(var ind in classes){
            if (typeof(classes[ind]) != 'function'){
                container.select('.' + classes[ind]).each(function(el){
                    el.removeClassName(classes[ind]);
                    el.addClassName('am_' + classes[ind]);
                });
            }
        }
        
    },
    showPleaseWait: function(hide, container){
        
        if (container){

        }
        
        var overlay = $('amOverlay');

        overlay.setStyle({
            'width' : $(overlay.parentNode).getStyle('width'),
            'height' : $(overlay.parentNode).getStyle('height')
        });

        overlay.setStyle({
            'display': hide ? 'none' : ''
        });




        var loading = $('am_loading');
        this.mainContainer.appendChild(loading);

        loading.setStyle({
            'visibility': hide ? 'hidden' : ''
        });

        var left = this.mainContainer.cumulativeOffset().left + (parseInt(this.mainContainer.getStyle('width')) - 44 )/2

        var top = this.mainContainer.cumulativeOffset().top + (parseInt(this.mainContainer.getStyle('height')) - 44 )/2

        loading.setStyle({
            'left': left+'px',
            'top': top+'px'
        });
        
    },
    createElement: function(html){
        var div = new Element("div");
        div.insert( html );
        return div.firstDescendant();  
    },
    _preloadHandlers:{
        login: function(container){
            container = container.down('#login-form');
            this.containers.login = container;
            return this.containers.login;
        },
        billing: function(container){
            container = container.down('#checkout-step-billing');
            this.containers.billing = container;
            return this.containers.billing;
        },
        shipping: function(container){
            container = container.down('#checkout-step-shipping');
            this.containers.shipping = container;
            return this.containers.shipping;
            
        },
        shipping_method: function(container){
            container = container.down('#checkout-step-shipping_method');
            container.down('#shipping-method-buttons-container').remove();
            this.containers.shipping_method = container;
            return this.containers.shipping_method;
        },
        payment: function(container){
            container = container.down('#checkout-step-payment');
            this.containers.payment = container;
            return this.containers.payment;
        },
        review: function(container){
            container = container.down('#checkout-step-review');
            this.containers.review = container;
            return this.containers.review;
        }
    },
    hideRows: function(container){
        container.select('#am_field_row').each(function(row){
            if (!row.getAttribute('nohide'))
                row.hide();
        });
        
    },
    showRows: function(container){
        container.select('#am_field_row').each(function(row){
            if (!row.getAttribute('noshow'))
            row.show();
        });
        this.resizeFields(container);
    },
    overrideEvents: function(){
        var _caller = this;
        if (typeof(billing) != 'undefined')
            billing.newAddress = function (isNew){
                if (isNew) {
                    this.resetSelectedAddress();
                   _caller.showRows( _caller.containers.billing);
                } else {
                    _caller.hideRows(_caller.containers.billing);
                }
                _caller.saveBilling();
            }
        
        if (typeof(shipping) != 'undefined')
            shipping.newAddress = function (isNew){
                if (isNew) {
                    this.resetSelectedAddress();
                    _caller.showRows( _caller.containers.shipping);
                    } else {
                    _caller.hideRows( _caller.containers.shipping);
                }
                shipping.setSameAsBilling(false);
                _caller.saveShipping();
            }
        
        if (typeof(review) != 'undefined')
            review.save = function (){
                
                _caller.saveBilling(true);
                
                return ;
        }
        
        if (this.options.reloadShippingAfterPaymentCheck && payment){
            if (typeof(payment.defaultSwitchMethod) != 'function'){
                eval('var tmpHandler  = ' + payment.switchMethod.toString());
                payment.defaultSwitchMethod = tmpHandler;
            }
                
            
            payment.switchMethod = function(method){
                if (payment.currentMethod != method){
                    _caller.savePayment(false, true);
                }
                
                payment.defaultSwitchMethod.call(payment, method);
                
            }
        }
    },
    runCheckout: function(){
        var _mainCaller = this;
        if (typeof(review) != 'undefined'){
            
            function registerUser(method, afterHandler){
                
                if (method) {
                    var request = new Ajax.Request(
                        _mainCaller.options.methodSaveUrl,
                        {
                            method: 'post',
                            onSuccess: function(transport){
                                if (method == 'register'){
                                    _mainCaller.saveBilling(true, afterHandler)
                                } else {
                                    afterHandler(transport);
                                }
                                
                            },
                            parameters: {
                                method: method
                            }
                        }
                    );

                } else {
                    afterHandler()
                }
            }
            
            function run(){
                var _caller = this;
                
                if ($('form_review'))
                {
                    var formReview = $('form_review');
                    var validator = new Validation(formReview);
                    if (validator.validate()) {
                        if (checkout.loadWaiting!=false) return;
                            checkout.setLoadWaiting('review');
                            var params = Form.serialize(payment.form);
                            if (this.agreementsForm) {
                            params += '&'+Form.serialize(this.agreementsForm);
                        }
                        var formReviewParam = Form.serialize(formReview);
                        params += '&' + formReviewParam; 
                        params.save = true;
                        var request = new Ajax.Request(
                            this.saveUrl,
                            {
                                method:'post',
                                parameters:params,
                                onComplete: function(){
                                    _mainCaller.showPleaseWait(true, _mainCaller.containers.review);

                                    registerUser('guest', function(){
                                        _caller.onComplete();
                                    });

                                },
                                onSuccess: this.onSave,
                                onFailure: checkout.ajaxFailure.bind(checkout)
                            }
                        );
                     }
                }
                else{
                    if (checkout.loadWaiting!=false) return;
                    checkout.setLoadWaiting('review');
                    var params = Form.serialize(payment.form);
                    if (this.agreementsForm) {
                        params += '&'+Form.serialize(this.agreementsForm);
                    }
                    params.save = true;
                    var request = new Ajax.Request(
                        this.saveUrl,
                        {
                            method:'post',
                            parameters:params,
                            onComplete: function(){
                                _mainCaller.showPleaseWait(true, _mainCaller.containers.review);

                                registerUser('guest', function(){
                                    _caller.onComplete();
                                });

                            },
                            onSuccess: this.onSave,
                            onFailure: checkout.ajaxFailure.bind(checkout)
                        }
                    );
            }
            }

            var billingCreateAccount = this.mainContainer.down("[id='billing:create_account']");
            
            if (billingCreateAccount) {
                
                registerUser(billingCreateAccount.checked ? 'register' : 'guest', function(){
                    run.call(review);
                });
                
            } else {
                registerUser('register', function(){
                    run.call(review);
                });
            }
        }
    },
    initDynamicEvents: function(collection, actionHandler){
        for(var attribute in collection){
            var fields = collection[attribute];
            for(var ind in fields){
                if (typeof(fields[ind]) != 'function'){
                      this.mainContainer.select('[' + attribute + '="' + fields[ind] + '"]').each(function(inputObj){
                          var event = inputObj.tagName.toLowerCase() == 'select' ? 'change' : 'blur';

                          inputObj.observe(event, function(){
                              if (this.tagName.toLowerCase() == 'input'){
                                  
                                    if (this.getAttribute('prev_val') != this.value){
                                        actionHandler();
                                        this.setAttribute('prev_val', this.value);
                                    }
                                    
                              } else
                                actionHandler();
                          });
                          
                          if (inputObj.tagName.toLowerCase() == 'input'){
                              inputObj.setAttribute('prev_val', inputObj.value);
                          }
                      })
                }
            }
        }
    },
    initEvents: function(){
      var _caller = this;
      
      function hideShippingArea(show){
          var shipping = _caller.mainContainer.down("#am_area_shipping");
          if (shipping)
            shipping.setStyle({
                'display': show ? '' : 'none'
            });
      }
      
      var use_for_shipping_yes = this.mainContainer.down("[id='billing:use_for_shipping_yes']");
      
      if (use_for_shipping_yes)
        use_for_shipping_yes.observe('click', function(){
            hideShippingArea();
            _caller.saveBilling();
        });
      
      var use_for_shipping_no = this.mainContainer.down("[id='billing:use_for_shipping_no']");
      
      if (use_for_shipping_no)
        use_for_shipping_no.observe('click', function(){
            hideShippingArea(true);
             _caller.saveBilling();
        });
        
      if (use_for_shipping_yes && use_for_shipping_no)
        hideShippingArea(use_for_shipping_no.checked);
      
      var same_as_billing = this.mainContainer.down("[id='shipping:same_as_billing']");
      
      if (same_as_billing)
        same_as_billing.observe('click', function(){
            _caller.mainContainer.down("[id='billing:use_for_shipping_yes']").click();
             _caller.saveShipping();
        });
      
        this.initDynamicEvents(this.billingRefreshableFields, function(){
            _caller.saveBilling();
        });
        
        this.initDynamicEvents(this.shippingRefreshableFields, function(){
            _caller.saveShipping();
        })
      
      
      var billingCreateAccount = this.mainContainer.down("[id='billing:create_account']");
      if (billingCreateAccount) {
            billingCreateAccount.observe('click', function(){
              var ch = this;
              _caller.containers.billing.select("[rel='customer_password']").each(function(div){
                  div.setStyle({
                      'display': ch.checked ? '' : 'none'
                  })
                  
              });
              
              _caller.resizeFields(_caller.containers.billing);

            });
      }
      
      this.mainContainer.select("[name='shipping_method']").each(function(input){

            input.observe('click', function(){
                _caller.saveShippingMethod();
            });
        });
      
      if (payment && payment.initWhatIsCvvListeners)
        payment.initWhatIsCvvListeners();
    
      if (typeof(amOrderattrConditionObj) != 'undefined'){
        var checked = $$('#co-shipping-method-form input:checked')[0];
        if (checked){
            var allowed_shipping_methods = amOrderattrConditionObj.conditions['shipping_methods'];
            amOrderattrConditionObj.onShippingMethodChange(checked, allowed_shipping_methods);
        }
      }
    },
    resizeFields: function(container){
        
        var possilibleInput = {'text': 1, 'password': 1};
        
        container.select("#am_field_row>input").each(function(input){

            var row = Element.up(input, '#am_field_row');
            
            if (input && possilibleInput[input.getAttribute('type')] &&
                row.style.display != 'none'){
                var decreaseWidth = 0;
                if (input.getAttribute('decreaseWidth')){
                    decreaseWidth = parseInt(input.getAttribute('decreaseWidth'));
                }
                
                input.setStyle({
                    'width': (row.getWidth() - decreaseWidth - 10) + 'px'
                });
            }
        })
        
        
        container.select("#am_field_row>textarea").each(function(textarea){
            
            var row = Element.up(textarea, '#am_field_row');
            if (textarea && row.style.display != 'none'){
                textarea.setStyle({
                    'width': (row.getWidth() - 10) + 'px'
                });
            }
        });
        
        container.select("#am_field_row>select").each(function(select){
            
            var row = Element.up(select, '#am_field_row');
            if (select && row.style.display != 'none'){
                select.setStyle({
                    'width': (row.getWidth() - 4) + 'px'
                });
            }
        });
    },
    rebuildSpecificField: function(container, form, field){
        if (field.is_eav_attribute == '1'){
            var tmpDiv = null;
            switch(field.field_eav_type){
                case "date":
                    var fieldLabel = container.down('[for="' + field.field_key + '"]');
                    var fieldInput = container.down('[id="' + field.field_key + '"]');
                    var fieldImg = container.down('[id="' + field.field_key + '_trig"]');
                    fieldInput.setStyle({
                        'width': ''
                    });
                    
                    fieldInput.setAttribute('decreaseWidth', 17); //icon width + padding
                    fieldInput.setStyle({'margin-right': '3px'})
                    tmpDiv = this.defaultRebuild(field, form, fieldLabel, fieldInput);
                    tmpDiv.appendChild(fieldImg);
                break;
                case "checkboxes":
                    var fieldLabel = container.down('[for="' + field.field_key + '"]');
                    if (fieldLabel){
                        var fieldInput = Element.up(fieldLabel, '.field-row').down('.checkboxes');
                    
                        tmpDiv = this.defaultRebuild(field, form, fieldLabel, fieldInput);
                    }
                    
                break;
                default:
                    tmpDiv = this.rebuildFieldByKey(container, form, field);
                    
            }
                    
            var anchor = container.down('[id="anchor_' + field.field_key + '"]');
            if (anchor){
                tmpDiv.appendChild(anchor);
            }
            
        }
        else if (!this.rebuildFieldByArea(container, form, field)){
            
            this.rebuildFieldByKey(container, form, field);
        }
            
    },
    getFieldContainer: function (field){
        var tmpDiv = new Element('div');

        tmpDiv.addClassName('am_field_row');
        tmpDiv.id = 'am_field_row';

        tmpDiv.setStyle({
            width: field.column_position + '%',
            display: field.field_disabled == 1 ? 'none' : ''
        });

        if (field.field_disabled == 1){
            tmpDiv.setAttribute('noshow', 1)
        }
        

        return tmpDiv;

    },
    markAsRequred: function(field, form, fieldLabel, fieldInput){
        if (field.field_required == '1'){
                fieldLabel.addClassName('am_required');
                fieldLabel.innerHTML = fieldLabel.innerHTML + '<em>*<em/>';
                
                switch(fieldInput.type){
                    case "radio":
                    case "checkbox":
                        form.select('[name="' + fieldInput.getAttribute('name') + '"]').each(function(input){
                            input.removeClassName('validate-one-required');
                        })
                        fieldInput.addClassName('validate-one-required');
                        break;
                    default:
                        fieldInput.addClassName('required-entry');
                    break;
                }
            }
            else{
                fieldInput.removeClassName('required-entry');
            }
    },
    defaultRebuild: function(field, form, fieldLabel, fieldInput){
        var tmpDiv = this.getFieldContainer(field);
         if (fieldLabel && fieldInput){
            
            
            switch(fieldInput.type){
                case "radio":
                case "checkbox":
                    var fieldObj = form.down('#am_field_row [name="' + fieldInput.getAttribute('name') + '"]');
                    if (fieldObj){
                        var row = fieldObj.up('[id="am_field_row"]');
                        if (row)
                            tmpDiv = row;
                    }
                    break;
                default:
                break;
            }

            fieldLabel.innerHTML = field.field_label;
            
            this.markAsRequred(field, form, fieldLabel, fieldInput);
                
            switch(fieldInput.type){
                case "radio":
                case "checkbox":
                    tmpDiv.appendChild(fieldInput);
                    tmpDiv.appendChild(fieldLabel);
                    tmpDiv.appendChild(new Element('br'));
                    break;
                default:
                    tmpDiv.appendChild(fieldLabel);
                    tmpDiv.appendChild(fieldInput);
                break;
            }
        
            
            
                form.appendChild(tmpDiv);

        }

        return tmpDiv;
    },
    rebuildFieldByKey: function(container, form, field){
        var _caller = this;
        var tmpDiv = null;
        
        function rebuild(){
            var fieldLabel = container.down('[for="' + field.field_key + '"]');
            var fieldInput = container.down('[id="' + field.field_key + '"]');
            
            var tmpDiv = _caller.defaultRebuild(field, form, fieldLabel, fieldInput);
            
            if (fieldInput && field.area_key == 'payment'){
                var fieldPaymentForm = container.down('[id="payment_form_' + fieldInput.value + '"]');
                if (fieldPaymentForm){
                    tmpDiv.appendChild(fieldPaymentForm);
                }
                
            }
            
            return tmpDiv;
        }
        
        switch(field.field_key){
            case "shipping:street1":
            case "billing:street1":
                var inputKey = field.field_key == 'billing:street1' ? 'billing[street][]' : 'shipping[street][]';
                var fieldLabel = container.down('[for="' + field.field_key + '"]');
                var fieldInputs = container.select("[name='" + inputKey + "']");
                
                if (fieldLabel && fieldInputs){
                    
                    tmpDiv = _caller.getFieldContainer(field);
                    tmpDiv.addClassName('am_field_multi_row');
                    var newLabel = fieldLabel.cloneNode(true);
                    
                    newLabel.innerHTML = field.field_label;
                    
                    if (field.field_required == 1){
                        newLabel.addClassName('am_required');

                        newLabel.innerHTML = newLabel.innerHTML + '&nbsp;<em>*<em/>';
                    }

                    tmpDiv.appendChild(newLabel);
                    
                    var marked = false;
                    for(var ind in fieldInputs){
                        var fieldInput = fieldInputs[ind];
                        if (typeof(fieldInput) == 'object'){
                            var newInput = fieldInput.cloneNode(true);
                            tmpDiv.appendChild(newInput);
                            fieldInput.parentNode.removeChild(fieldInput);
                            
                            if (!marked)
                            this.markAsRequred(field, form, fieldLabel, newInput);
                            
                            marked = true;
                        }
                        
                    }
                    
                    
//                    if (field.field_key == 'billing:street1')
//                        form.down('#billing-new-address-form fieldset').appendChild(tmpDiv);
//                    else
                        form.appendChild(tmpDiv);
                    
                    fieldLabel.parentNode.removeChild(fieldLabel);
                }
                
                
                break;
            case "shipping:region_id":
                var fieldLabel = container.down('[for="shipping:region"]');
                var fieldInput = container.down('[id="' + field.field_key + '"]');
                
                var fieldHiddenInput = container.down('[id="shipping:region"]');
                
                tmpDiv = _caller.defaultRebuild(field, form, fieldLabel, fieldInput);
                tmpDiv.appendChild(fieldHiddenInput);
                break;
            case "billing:region_id":
                var fieldLabel = container.down('[for="' + field.field_key + '"]');
                var fieldInput = container.down('[id="' + field.field_key + '"]');
                
                var fieldHiddenInput = container.down('[id="billing:region"]');
                
                tmpDiv = _caller.defaultRebuild(field, form, fieldLabel, fieldInput);
                tmpDiv.appendChild(fieldHiddenInput);
                
                break;
            case "shipping-address-select":
            case "shipping:same_as_billing":
            case "billing:use_for_shipping_yes":
            case "billing:use_for_shipping_no":
            case "billing-address-select":
                tmpDiv = rebuild();
                tmpDiv.setAttribute('nohide', '1');
            break;
//            case 'p_method_ccsave':
//                var tmpDiv = rebuild();
//                var ccsave = container.down('#payment_form_ccsave');
//                if (ccsave)
//                tmpDiv.appendChild(ccsave);
//            break;
            case "billing:create_account":
                if (container.down('[id="billing:customer_password"]')){
                    if (_caller.options.isGuestCheckoutEnabled){
                        var fieldLabel = new Element('label');
                        var fieldInput = new Element('input');
                        fieldInput.type = 'checkbox';
                        fieldLabel.setAttribute('for', field.field_key);
                        fieldInput.setAttribute('id', field.field_key);
                    tmpDiv = _caller.defaultRebuild(field, form, fieldLabel, fieldInput);
                    }
                    
                }
            break;
            case "billing:customer_password":
            case "billing:confirm_password":
                
                tmpDiv = rebuild();
                
                if (_caller.options.isGuestCheckoutEnabled){
                    tmpDiv.setStyle({
                        'display': 'none'
                    });
                }
                
                tmpDiv.setAttribute('rel', 'customer_password');
                
                
                break;
                    
            default:
                tmpDiv = rebuild();

                break;
        }
        return tmpDiv;
    },
    rebuildFieldByArea: function(container, form, field){
        var ret = false;
        var area = this.getAreaById(field.area_id);
        switch(area.area_key){
            case "shipping_method":
                
                var fieldLabel = container.down('[for="' + field.field_key + '"]');
                var fieldInput = container.down('[id="' + field.field_key + '"]');
                if (fieldLabel && fieldInput){

                    var carierLabel = null;

                    var dd = Element.up(fieldInput, 'dd');
                    
                    if (dd){
                        
                        var dt = dd.previousSibling;
                        
                        if (!dt.tagName || dt.tagName.toLowerCase() !== 'dt')
                            dt = dt.previousSibling;
                        
                        if (dt && dt.tagName && dt.tagName.toLowerCase() == 'dt'){
                           carierLabel = dt.innerHTML;
                           try{
                               dt.parentNode.removeChild(dt);
//                               dt.remove();
                           } catch(e){
                               
                           }
                           
                        }
                    }
                    
                    var price = fieldLabel.down('span.price').cloneNode(true);
                    
                    var tmpDiv = this.defaultRebuild(field, form, fieldLabel, fieldInput);
                    
                    fieldLabel.appendChild(price);
                    
                    if (carierLabel){
                        var carierDiv = new Element('div');
                        carierDiv.addClassName('am_carrier_label');
                        carierDiv.innerHTML = carierLabel;
                        tmpDiv.insertBefore(carierDiv, fieldInput);
                    }

                    var fieldShippingForm = container.down('[id="shipping_method_form_' + fieldInput.value + '"]');
                    if (fieldShippingForm){
                        tmpDiv.appendChild(fieldShippingForm);
                    }

                }
                
                
                ret = true;
            break;
        }
        return ret;
    },
    rebuildDOM: function(area, container){  
        var form = container;
            
        if (container.tagName.toLowerCase() != 'form')
            form = container.down('form');

        if (this.amorderattr[area.area_key]){
            
            var tmpDiv = new Element('div')
            tmpDiv.innerHTML = this.amorderattr[area.area_key].outerHTML;
            form.appendChild(tmpDiv.firstChild);
        }
        
        for(var fieldOrder in this.fields[area.area_id]){
            var field = this.fields[area.area_id][fieldOrder];
            if (typeof(field) == 'object'){
                this.rebuildSpecificField(container, form, field);
            }
        }
        
        switch(area.area_key){
            case "billing":
                var billing = container.down('[id="billing-address-select"]')
                var guestCaptcha = container.down('#captcha-input-box-guest_checkout');
                var captcha = container.down('#captcha-input-box-register_during_checkout');
                
                var guestCaptchaImage = container.down('#captcha-image-box-guest_checkout');
                var captchaImage = container.down('#captcha-image-box-register_during_checkout');
                    
                if (billing && billing.value){
                    this.hideRows(this.containers.billing);
                    if (guestCaptcha && guestCaptchaImage){
                        guestCaptcha.hide();
                        $(guestCaptchaImage.parentNode).hide();
                    }
                        
                } else {
                    if (captcha && captchaImage){
                        captcha.hide();
                        $(captchaImage.parentNode).hide();
                    }
                }
            break;
            case "shipping":
                var shipping = container.down('[id="shipping-address-select"]')
                if (shipping && shipping.value){
                    this.hideRows(this.containers.shipping);
                }
            break;
        }
        
    },
    initSection: function(area, column){

        var container = this.checkoutSteps.down('#opc-' + area.area_key);

        if (container){
            
            var opcDiv = new Element('div');
            opcDiv.id = container.id;
            container.id = null;
            
            var opcButton = container.down('button');
            if (opcButton){
                opcButton.setStyle({'display': 'none'})
                opcDiv.appendChild(opcButton);
            }
                
            
            container.down('#checkout-step-' + area.area_key).setStyle({
                'display': ''
            });
            container.select('li').each(function(li){
                li.removeClassName('wide');
            });

            var buttonsContainer = container.down('#' + area.area_key + '-buttons-container');

            if (buttonsContainer && area.area_key != 'review')
                buttonsContainer.parentNode.removeChild(buttonsContainer);

            this.replaceClassesSets(container, [
                'fields', 'field', 'input-box',
                'form-list', 'fieldset'
            ]);
            
            var amorderattr = container.down('#amorderattr');
            if (amorderattr){
                this.replaceClassesSets(amorderattr, [
                    'fields', 'field', 'input-box',
                    'form-list'
                ]);
                
//                var tmpDiv = new Element('div')
//                tmpDiv.innerHTML = amorderattr.outerHTML;
                
                this.amorderattr[area.area_key] = amorderattr.cloneNode(true);//tmpDiv.firstChild;
                
                amorderattr.remove();
            }
            
            var amcustomerattr = container.select('.amcustomerattr');
            amcustomerattr.each(function(el){
                el.hide();
            })
            
            if (typeof(this._preloadHandlers[area.area_key]) == 'function'){
                var ret = this._preloadHandlers[area.area_key].call(this, container);
                if (typeof(ret) == 'object')
                    container = ret;
            }

            this.rebuildDOM(area, container);

            var html = [];

            html.push('<h2 class="am_header">', area.area_label, '</h2>');

            var tmpLi = new Element('li');
            tmpLi.id = 'am_area_' + area.area_key;
            tmpLi.appendChild(this.createElement(html.join('')));
            
            
            opcDiv.appendChild(container);
            
            tmpLi.appendChild(opcDiv);

            this.mainContainer.down('#' + column + '_column ul').appendChild(tmpLi);

            this.resizeFields(opcDiv);
        }
        
    },
    initScripts: function(container){
        var _caller = this;
        container.select('script').each(function(script){
            _caller.scripts.push(script);
        });
    },
    basicInit: function(noCallAfterHandler){
        var _caller = this;
        
        _caller.overrideEvents();

        var form = $(billing.form);
        var data = form.serialize(true);

        var area = _caller.getArea('billing');

        var fields = _caller.fields[area.area_id];
        _caller.fillDefaultData(data, form, fields);

         new Ajax.Request(
         _caller.options.afterSaveUrl,
         {
            parameters: data,
            onSuccess: function(){
                
                if (!noCallAfterHandler)
                    _caller.saveBilling();
            }
        });
    },
    runScripts: function(){
        var _caller = this;
        
        for(var ind in this.scripts){
            var script = this.scripts[ind];
            if (typeof(script) == 'object'){
                if (script.src){
                    
                    $$("head")[0].insert(new Element("script", {
                        type: "text/javascript",
                        src: script.src
                    }));
                }
            }
        }
        
        var maxIterations = 10;
        
        function runDynamicScripts(){
             
            try{
                for(var ind in _caller.scripts){
                    var script = _caller.scripts[ind];
                    if (typeof(script) == 'object'){
                        if (!script.src){                            
                            var objScript = new Element('script', {
                                type: 'text/javascript'
                            })
                            $$('head').first().insert({
                                bottom: objScript
//                                .update(script.innerHTML)
                            });
                            
                            if (script.innerHTML.indexOf('amOnepageCheckoutObj') == -1 ){
                            if (Prototype.Browser.IE){
                                var content = Object.toHTML(script.innerHTML);
                                objScript.text = content;
                            } else
                                objScript.update(script.innerHTML)
                            }
                            
                        }
                    }
                }
                
                _caller.basicInit();

            } catch(e){
                
                maxIterations -- ;
                if (maxIterations > 0)
                    window.setTimeout(runDynamicScripts, 1000)
            }
        }
        
        window.setTimeout(runDynamicScripts, 500);
    },
    getColumnsCount: function(layoutType){
        var columnsCount = 3;
        
        if (layoutType == 'two_columns')
            columnsCount = 2;
        else if (layoutType == 'one_column'){
            columnsCount = 1;
        }
        
        return columnsCount;
    },
    initialize: function(areas, fields, options)
    {
        var _caller = this;
        this.areas = areas;
        this.fields = fields;
        this.options = options;
        
        if (options.billingRefreshableFields){
            for(var attribute in options.billingRefreshableFields){
                var fields = options.billingRefreshableFields[attribute];
                for(var ind in fields){
                    if (typeof(fields[ind]) != 'function'){
                        this.billingRefreshableFields[attribute].push(fields[ind]);
                    }
                }
            }
        }
        
        if (options.shippingRefreshableFields){
            for(var attribute in options.shippingRefreshableFields){
                var fields = options.shippingRefreshableFields[attribute];
                for(var ind in fields){
                    if (typeof(fields[ind]) != 'function'){
                        this.shippingRefreshableFields[attribute].push(fields[ind]);
                    }
                }
            }
        }
        
        this.mainContainer = $('am_onepage_container');
        
                    _caller.checkoutSteps = $('checkoutSteps');

                    var areasPerColumn = Math.ceil(_caller.areas.length / _caller.getColumnsCount(options.layoutType));
                    var column = 1;

//                    _caller.initScripts($(document.body));

                    for(var order in _caller.areas){
                        var area = _caller.areas[order];

                        if (typeof(area) == 'object'){

                            if (column * areasPerColumn < (parseInt(order) + 1)){
                                column++;

                            }

                            if (options.layoutType == 'two_columns' && area.area_key == 'shipping_method'){
                                column = 2;
                            }

                            _caller.initSection(area, column);

                        }
                    }
                    
                    _caller.moveDiscountCouponForm();
                    
//                    _caller.runScripts();
                    _caller.initEvents();
                    _caller.saveBilling();
//                    _caller.savePayment();
        _caller.showPleaseWait(false);


        this.initShoppingCart();
    },
    initShoppingCartData: function(form, data){
        var storeConfig = this.options.storeConfig;

        var qtyIndex = 2;  
        var productIndex = 0;

        var trOrder = 0;
        

        data.select('tbody tr').each(function(tr){
            var links = tr.select('a');
            var editLink, viewLink, deleteLink;

            links.each(function(link){
                if (link.hasClassName('btn-remove')){
                    deleteLink = link;
                } else if($(link.parentNode).hasClassName('product-name')){
                    viewLink = link;
                } else {
                    editLink = link;
                }
            });

            var targetTr = form.select('#shopping-cart-table tbody tr')[trOrder];
            var tfootTr = form.select('#shopping-cart-table tfoot tr')[0];

            if (targetTr){
                if (storeConfig.shopping_cart.qty_updatable == 1){
                    var inputQty = data.down('input.qty');

                    var qtyCell = targetTr.select('td')[qtyIndex];
                    qtyCell.innerHTML = '';
                    qtyCell.appendChild(inputQty);
                }
                
                if (storeConfig.shopping_cart.editable == 1){
                    var productLink = editLink && storeConfig.shopping_cart.editable ? editLink : viewLink;

                    var productCell = targetTr.select('td')[productIndex];
                    productLink.innerHTML = viewLink.innerHTML;

                    productCell.innerHTML = '';
                    productCell.appendChild(productLink);
                }
                
                
                if (storeConfig.shopping_cart.delitable == 1 && deleteLink){
//                    var tmpSpan = new Element('span');
//                    
//                    tmpSpan.setStyle({
//                        'float': 'right'
//                    });
                    
//                    productLink.setStyle({
//                        'display': 'table-cell'
//                    })
                    
//                    productCell.appendChild(tmpSpan);
//                    tmpSpan.appendChild(deleteLink);
                    var tmpTd = targetTr.appendChild(new Element('td'))
                    tmpTd.appendChild(deleteLink);
                    tmpTd.setStyle({
                        'padding': '3px 2px'
                    })
                    
                }

            }

            trOrder++;
        })
        
        if (storeConfig.shopping_cart.delitable == 1){
           form.down('#shopping-cart-table thead tr').appendChild(new Element('th'));
//           form.down('#shopping-cart-table tfoot tr').appendChild(new Element('td'));
        }

        if (storeConfig.shopping_cart.qty_updatable == 1){
            form.down('#shopping-cart-table').appendChild(data.down('tfoot'));
            
            form.down('#shopping-cart-table').select('button').each(function(button){
                
                if (!button.hasClassName('btn-update')){
                    button.remove();
                }
            })
        }
    },
    initShoppingCartEvents: function(shoppingCartForm){
        
        var _caller = this;
        var shoppingCartTable = shoppingCartForm.down('#shopping-cart-table');
        var updateCartAction = shoppingCartTable.down('button.btn-update');
        if (updateCartAction){
            updateCartAction.setAttribute('type', 'button');


            updateCartAction.observe('click', function(){

                _caller.showPleaseWait(false, shoppingCartTable);

                var form = Element.up(shoppingCartTable, 'form');
                if (form){
                    var url = form.getAttribute('action');
                    var data = form.serialize(true);

                    data["update_cart_action"] = "update_qty";
                    var request = new Ajax.Request(
                        _caller.options.shoppingCartSaveUrl,
                        {
                            method: 'post',
                            onSuccess: function(transport){
                                _caller.showPleaseWait(false, _caller.containers.billing);

                                var response = eval('(' + transport.responseText + ')');
                                _caller.updateSection(response);

                                _caller.basicInit();

                            },
                            parameters: data
                        }
                    );
                }

                return false;
            });
        }
        
        var btns = shoppingCartTable.select('.btn-remove');
        if (btns.length > 0)
            btns.each(function(a){
                a.observe('click', function(){
                    _caller.showPleaseWait(false, shoppingCartTable);

                    var url = a.getAttribute('href');
                    url= url.replace('checkout', 'amscheckout');
                    var request = new Ajax.Request(
                        url,
                        {
                            onSuccess: function(transport){
                                _caller.showPleaseWait(false, _caller.containers.billing);

                                var response = {};
                                try{
                                    var response = eval('(' + transport.responseText + ')');
                                    _caller.updateSection(response);

                                    _caller.basicInit();
                                }    
                                 catch(e){
                                    document.location.reload();
                                }
                            }
                        }
                    );
                    a.setAttribute('href', '#')

                    return false;
                })

             });
    },
    initShoppingCart: function(notReloadReview){
        var shoppingCartTable = $('shopping-cart-table');
        if (shoppingCartTable){
            
            var shoppingCartForm = Element.up(shoppingCartTable, 'form');

            var storeConfig = this.options.storeConfig;

            if (storeConfig.shopping_cart.cart_to_checkout == 1){
                shoppingCartForm.hide();

                if (this.containers.review && !notReloadReview){

                    shoppingCartForm = shoppingCartForm.cloneNode(true);
                    shoppingCartForm.show();
                    var shoppingCartData = shoppingCartForm.removeChild(shoppingCartForm.down('fieldset'));

                    if (shoppingCartData){

                        var tmpTable = new Element('table');

                        tmpTable.appendChild(this.containers.review.down('thead'));

                        var tmpTbody = tmpTable.appendChild(new Element('tbody'));

                        var reviewTbody = this.containers.review.down('tbody');

                        this.containers.review.select('tbody tr').each(function(tr){
                            tmpTbody.appendChild(tr)
                        });

                        var reviewTd = reviewTbody.appendChild(new Element('tr')).appendChild(new Element('td'));//innerHTML = '<tr><td colspan="100" ></td></tr>';

                        reviewTd.setAttribute('colspan', 4);
                        reviewTd.addClassName('shopping_cart_review')

                        reviewTbody.down('td').appendChild(shoppingCartForm);

                        shoppingCartForm.appendChild(new Element('fieldset')).appendChild(tmpTable);

                        tmpTable.setAttribute('id', 'shopping-cart-table')

                        this.initShoppingCartData(shoppingCartForm, shoppingCartData);

                    }

                }
            } else {
                if (storeConfig.shopping_cart.qty_updatable == '0'){
                    $$('#shopping-cart-table tfoot')[0].remove();
                }
                
                if (storeConfig.shopping_cart.delitable == '0'){
                    $$('#shopping-cart-table a.btn-remove').each(function(item){item.remove()})
                }
                
                if (storeConfig.shopping_cart.editable == '0'){
                    $$('#shopping-cart-table a').each(function(item){item.setAttribute('href', '#')})
                }
            }

            this.initShoppingCartEvents(shoppingCartForm);
        }
        
    },
    setDeafultRadio: function(container){
        if (!container.down('input[type=radio][checked=true]')){
            var radio = container.down('input[type=radio]');

            if (radio){
                radio.click();
            }
                
        }
    },
    updateSection: function(response, options){
        if (!options)
            options = {};
        
        var _caller = this;
        
        function runScripts(container){
            var maxIterations = 10;
            
            function subRunScripts(){
                
                    container.select('script').each(function(script){
                    try{
                        eval(script.innerHTML);
                        _caller.overrideEvents();
                        delete script;
                } catch(e){
                    maxIterations--;
                    
                    if (maxIterations > 0)
                    {
                        window.setTimeout(subRunScripts, 1000)
                    }
                }
                })
                
            }
            
            subRunScripts();
            
        }
        
        switch(response.update_section.name)
        {
            case "shipping-method":
                if (!options.checkRequired){
                    var container = _caller.containers.shipping_method.down('[id="co-shipping-method-form"]');
                    
                    container.childElements().each(function(item){
                        if (item.id != 'onepage-checkout-shipping-method-additional-load')
                            item.remove();
                    });
                    var tmpDiv = new Element('div')
                    tmpDiv.innerHTML = response.update_section.html;
                    container.appendChild(tmpDiv)
//                    container.innerHTML = response.update_section.html;

                    var area = _caller.getArea('shipping_method');

                    _caller.rebuildDOM(area, container);
                    _caller.resizeFields(container);

                    _caller.mainContainer.select("[name='shipping_method']").each(function(input){

                        input.observe('click', function(){
                            _caller.saveShippingMethod();
                        });
                    });  
                    
                    runScripts(container);
                    
//                    if (!container.down('input[type=radio][checked=true]')){
//                        var radio = container.down('input[type=radio]');
//                        
//                        if (radio)
//                            radio.setAttribute("checked", "true")
//                    }
//                    
                    _caller.setDeafultRadio(container);
                    
                    _caller.showPleaseWait(true, _caller.containers.shipping_method);
                }
                
                if (!options.noRecursive)
                    _caller.saveShippingMethod(options.checkRequired, options.skipSaveDefault);
                
                break;
            case "payment-method":
                
                if (!options.checkRequired){
                    var container = _caller.containers.payment.down('[id="co-payment-form"]');

                    var area = _caller.getArea('payment');

                    container.innerHTML = response.update_section.html;

                    _caller.replaceClassesSets(container, [
                        'fields', 'field', 'input-box',
                        'form-list'
                    ]);
            
                    _caller.rebuildDOM(area, container);
                    _caller.resizeFields(container);

                    _caller.mainContainer.select("[name='payment[method]']").each(function(input){

                        input.observe('click', function(){
                            _caller.resizeFields(_caller.containers.payment)
//                            _caller.savePayment();
                        });
                    });

                    runScripts(container);
                    
//                    if (!container.down('[checked=true]')){
//                        var radio = container.down('input[type=radio]');
//                        if (radio)
//                            radio.setAttribute("checked", "true")
//                    }
                    _caller.setDeafultRadio(container);
                    _caller.showPleaseWait(true, _caller.containers.payment);
                }
                
                if (payment && payment.initWhatIsCvvListeners)
                payment.initWhatIsCvvListeners();
                
                if (!options.noRecursive)
                    _caller.savePayment(options.checkRequired);
                break;
            case "review":
                if (!options.checkRequired){
                    var container = _caller.containers.review.down('[id="checkout-review-load"]');
                    
                    var area = _caller.getArea('review');
                    
                    
                    var form_review = container.down("#form_review");
                    var form_data = form_review ? form_review.serialize(true) : null;
                    
                    container.innerHTML = response.update_section.html;
                    
                    if (form_data){
                        for (var name in form_data){
                            var el = container.down("[name='" + name + "']");
                            if (el){
                                el.value = form_data[name];
                            }
                        }
                        
                    }
                    
                    _caller.rebuildDOM(area, container);
                    _caller.resizeFields(container);
                    
                    runScripts(container);
                    
                    var shopping_cart_table = $('shopping-cart-table');
                    
                    if (shopping_cart_table && response.update_section.checkout_cart){
                        _caller.updateShoppingCartTable(response.update_section.checkout_cart);
                    
//                        var tmpDiv = new Element('div');
//                        tmpDiv.innerHTML = response.update_section.checkout_cart;
//
//                        var new_shopping_cart_table = tmpDiv.down('#shopping-cart-table');
//                        if (new_shopping_cart_table){
//
//                            shopping_cart_table.parentNode.appendChild(new_shopping_cart_table);
//                            shopping_cart_table.remove();
//                           _caller.showPleaseWait(true, _caller.containers.review);
//                           
//                           _caller.initShoppingCart();
//                        }
                    }
                    
                    $('am_area_review').setStyle({"display": "block"})
                    
                    
                    _caller.showPleaseWait(true, _caller.containers.review);
                    
                } else{
                    _caller.runCheckout();
                }
                
                break;
        }
    },
    updateShoppingCartTable: function(html){
        var shopping_cart_table = $('shopping-cart-table');
        
        var tmpDiv = new Element('div');
        tmpDiv.innerHTML = html;

         var new_shopping_cart_table = tmpDiv.down('#shopping-cart-table');
         if (new_shopping_cart_table){

            shopping_cart_table.parentNode.appendChild(new_shopping_cart_table);
            shopping_cart_table.remove();
            this.showPleaseWait(true, this.containers.review);

            this.initShoppingCart();
        } 
        
        
        tmpDiv.select('script').each(function(script){
            eval(script.innerHTML);
        })
        
        this.moveMessages(tmpDiv);
    },
    saveDefaults: function(){
        this.showPleaseWait(false, this.containers.shipping_method);
        this.updateLayout(this.options.saveDefaultsUrl, {}, {
            onErrorShowAlert: true,
            skipSaveDefault: true
        });
    },
    updateLayout: function(url, data, options){
        if (!options)
            options = {};
        
        var _caller = this;
        
        var request = new Ajax.Request(
            url,
            {
                method: 'post',
                onSuccess: function(transport){
                    _caller.mainContainer.show();
                    var isContinue = true;
                    
                    var response = eval('(' + transport.responseText + ')');
                    
                    if (options.checkRequired && response.error) {
                        alert(response.error);
                        _caller.showPleaseWait(true, _caller.containers.review);
                    } else {
                        if (typeof(options.afterHandler) == 'function'){
                            isContinue = options.afterHandler(transport);
                        }

                        if (isContinue){
                            
                            if (response.redirect){
                                var link = new Element('a')
                                link.innerHTML = '<button type="button" title="Redirect" class="button redirect-btn"><span><span> ' + _caller.options.redirectLabel + ' </span></span></button>'
                                link.href = response.redirect;
                                var review = _caller.containers.review.down('[id="checkout-review-load"]');

                                review.innerHTML = '';
                                review.appendChild(link);
                                
                                _caller.showPleaseWait(true, _caller.containers.review);
                                
                                $('am_area_review').setStyle({"display": "block"})
//                                review.app
//                                document.location.href = response.redirect;

                            } else if (!response.error){
                                if (response.update_section){
                                    _caller.updateSection(response, options);
                                } 
                                else if (response.goto_section){
                                    switch(response.goto_section){
                                        case "shipping":
                                           _caller.saveShipping(options.checkRequired);
                                            break;
                                    }
                                }
                            } else { //else if (options.checkRequired){
                                var message = '';

                                if (typeof(response.message) == 'object')
                                    message = response.message.join('\n');
                                else if (typeof(response.error) == "string"){
                                    message = response.error;
                                }
                                else
                                    message = response.message;

                                _caller.showPleaseWait(true, _caller.containers.review);

                                if (options.onErrorShowAlert || options.checkRequired) {
                                    alert(message);
                                } else {
                                    if (!options.skipSaveDefault)
                                    _caller.saveDefaults();
                                }


                            }
                        }
                    }
                    
                },
                on403: function(){
                    document.location.reload();
//                    _caller.mainContainer.hide();
                },
                parameters: data
            }
        );
    },
    fillDefaultData: function(data, form, fields, checkRequired){
        for(var ind_f in fields){
            var field = fields[ind_f];
            if (!checkRequired || field.field_required == '0'){
                var name = null;
                var fieldObj = $(form.down('[id="' + field.field_key + '"]'));

                if (fieldObj){
                    name = fieldObj.getAttribute('name');
                }

                switch(field.field_key){                    
                    case "billing:street1":
                        if (data['billing[street][]'] == '')
                            data['billing[street][]'] = '-';
                        
                        if (data['billing[street][]'][0] == '')
                            data['billing[street][]'][0] = '-';
                        break;
                    case "billing:email":
                        if (data[name] == '')
                            data[name] = 'email@default.com';
                        break;
                    case "billing:region_id":
                        if (fieldObj[0].selected && fieldObj[1])
                          data[name] = fieldObj[1].value;
                        break;
                    case "billing:use_for_shipping_no":
                    case "billing:use_for_shipping_yes":
                    case "billing:country_id":
                    case "shipping-address-select":
                    case "billing-address-select":
                        break;
                    default:
                        if (data[name] == '')
                            data[name] = '-';
                }
            }       
        }
    },
    saveBilling: function(checkRequired, afterHandler, noRecursive){
        
        var validated = true;
        
        if (checkRequired){
            var validator = new Validation(billing.form);
            validated = validator.validate();
        }
        
        if (validated){
            this.showPleaseWait(false, this.containers.shipping_method);
            var form = $(billing.form);
            var data = form.serialize(true);

            var area = this.getArea('billing');


            var fields = this.fields[area.area_id];
            this.fillDefaultData(data, form, fields, checkRequired);
            
            this.updateLayout(this.options.billingSaveUrl, data, {
                'checkRequired': checkRequired, 
                'afterHandler': afterHandler,
                'noRecursive': noRecursive,
                'skipSaveDefault': true
            });
        } else {
            this.showPleaseWait(true, this.containers.shipping_method);
        }
    },
    saveShipping: function(checkRequired){
        
        var validated = true;
        
        if (checkRequired){
            var validator = new Validation(shipping.form);
            validated = validator.validate();
        }
        
        if (validated){
            this.showPleaseWait(false, this.containers.shipping_method);
        
            var form = this.mainContainer.down('#co-shipping-form');
            var data = form.serialize(true);

            var area = this.getArea('shipping');


            var fields = this.fields[area.area_id];

            for(var ind_f in fields) {
                var field = fields[ind_f];
                if (!checkRequired || field.field_required == '0'){
                    var name = null;
                    var fieldObj = form.down('[id="' + field.field_key + '"]');

                    if (fieldObj){
                        name = fieldObj.getAttribute('name');
                    }

                    switch(field.field_key){
                        case "shipping:street1":
                            if (data['shipping[street][]'][0] == '')
                                data['shipping[street][]'][0] = '-';
                        break;
                        case "shipping:region_id":
                            if (fieldObj[0].selected && fieldObj[1])
                              data[name] = fieldObj[1].value;
                        break;
                        case "shipping:country_id":
                        case "shipping-address-select":
                        break;
                        default:
                            if (data[name] == '')
                                data[name] = '-';
                    }
                }
            }


            this.updateLayout(this.options.shippingSaveUrl, data, {
                'checkRequired': checkRequired,
                'skipSaveDefault': true
            });
        } else {
            this.showPleaseWait(true, this.containers.shipping_method);
        }
        
    },
    saveShippingMethod: function(checkRequired, skipSaveDefault) {
        
        var validated = true;
        
        if (checkRequired){
            var validator = new Validation(shippingMethod.form);
            validated = validator.validate();
        }
        
        var form = this.mainContainer.down('#co-shipping-method-form');
        var data = form.serialize(true);
        
        if (validated && $H(data).keys().length > 0) {
            this.showPleaseWait(false, this.containers.payment);


            

            if (!checkRequired && !data.shipping_method){
    //            var area = this.getArea('shipping_method');
    //            var fields = this.fields[area.area_id];
    //            var method = this.mainContainer.down('#' + fields[0].field_key);
    //            data.shipping_method = method.value;

            }

            this.updateLayout(this.options.shippingMethodSaveUrl, data, {
                'checkRequired': checkRequired,
                'skipSaveDefault': skipSaveDefault
            });
        
        } else {
            this.setDeafultRadio(this.containers.payment);
            this.savePayment(checkRequired);
            //showPleaseWait(true, this.containers.shipping_method);
        }
        
    },
    savePayment: function(checkRequired, reloadShipping){
        
        var caller = this;
        var validated = true;
        
        var form = this.mainContainer.down('#co-payment-form');
        var data = form.serialize(true);
        
        if (checkRequired){
            var validator = new Validation(payment.form);
            validated = validator.validate();
        }
        
        
        
        if (validated && $H(data).keys().length > 0){
            
            
            var afterHandler;
            if (reloadShipping){
                this.showPleaseWait(false, this.containers.shipping_method);

                afterHandler = function(){
                    caller.saveBilling(false, null, true);
                    return true;
                }
            } else{
                this.showPleaseWait(false, this.containers.review);
            }

            var url = checkRequired ? this.options.paymentSaveUrl: this.options.paymentMethodSaveUrl;

            this.updateLayout(url, data, {
                'checkRequired': checkRequired,
                'onErrorShowAlert': true,
                'afterHandler': afterHandler
            });
        } else {
            this.showPleaseWait(true, this.containers.shipping_method);
        }
    },
    getArea: function(key){
        var areaRet;
        for (var ind in this.areas){
            var area = this.areas[ind];
            if (typeof(area) == 'object' && area.area_key == key){
                areaRet = area;
                break;
            }
        }
        return areaRet;
    },
    getAreaById: function(id){
        var areaRet;
        for (var ind in this.areas){
            var area = this.areas[ind];
            if (typeof(area) == 'object' && area.area_id == id){
                areaRet = area;
                break;
            }
        }
        return areaRet;
    },
    moveMessages: function(tmpDiv){
//        var cartDiv = $$('div.cart');
                    
        var messages = tmpDiv.down('ul.messages');

        if (messages){
            var cur_messages = $$('ul.messages');

            if (cur_messages[0]){
                cur_messages[0].innerHTML = messages.innerHTML;
            } else {

                this.mainContainer.parentNode.insertBefore(messages, this.mainContainer);
            }
        }
    },
    moveDiscountCouponForm: function(){
        var _caller = this;
        
        if (typeof(discountForm) == 'object'){
            
            var successHandler = function(transport){
                var tmpDiv = new Element('div');
                tmpDiv.innerHTML = transport.responseText;
                
                var cartDiv = $$('.col-main div.cart');
                
                if (cartDiv[0] && tmpDiv.down('div.cart'))
                    cartDiv[0].innerHTML = tmpDiv.down('div.cart').innerHTML;//tmpDiv.down('div.cart').innerHTML;

                _caller.moveMessages(tmpDiv);

                var newCart = tmpDiv.down('#discount-coupon-form');//.up()

                if (newCart)
                {
                    moveForm(newCart);
                    _caller.initShoppingCart(true);
                    _caller.savePayment();
//                    _caller.showPleaseWait(true, _caller.containers.payment);
//                    _caller.basicInit();
                }
            };
            
            if (typeof(discountForm.defaultSubmit) != 'function'){
                eval('var tmpHandler  = ' + discountForm.submit.toString());
                discountForm.defaultSubmit = tmpHandler;
            }

            
            var initDiscountForm = function() {
            discountForm.submit = function(isRemove){
                
                if (isRemove) {
                    $('coupon_code').removeClassName('required-entry');
                    $('remove-coupone').value = "1";
                } else {
                    $('coupon_code').addClassName('required-entry');
                    $('remove-coupone').value = "0";
                }
                
                var container = $('am_rewrited_discount').down('.am_cart');

                if (discountForm.validator && discountForm.validator.validate()){
                    _caller.showPleaseWait(false, container);

                    var url = _caller.options.couponPostUrl;

                    var request = new Ajax.Request(
                        url,
                        {
                            method: 'post',
                            onSuccess: function(transport){

                                successHandler(transport);

                            },
                            parameters: $('discount-coupon-form').serialize(true)
                        }
                    );
                }
                

            }
            }

            function moveForm(container){
                if (container){
//                    _caller.showPleaseWait(true, _caller.containers.review);

                    $$('[id=am_rewrited_discount]').each(function(li){
                        li.remove();
                    });

                    var am_area_review = _caller.mainContainer.down('#am_area_review');
                    var tmpLi = new Element('li');
                    tmpLi.id = 'am_rewrited_discount';

                    var tmpDiv = new Element('div')
                    var h2 = new Element('h2');
                    h2.innerHTML = '&nbsp;';

                    tmpDiv.addClassName('am_cart');

                    am_area_review.parentNode.insertBefore(tmpLi, am_area_review);

                    tmpLi.appendChild(h2);
                    tmpLi.appendChild(tmpDiv);
                    tmpDiv.appendChild(container);
                    
                    
                    $$('[name=amcoupon_code_cancel]').each(function(hidden){
                        Element.up(hidden, 'form').submit = function(){
                            _caller.showPleaseWait(false, container);
                            
                            var url = this.getAttribute('action');

                            var request = new Ajax.Request(
                                url,
                                {
                                    method: 'post',
                                    onSuccess: function(transport){
                                        
                                        successHandler(transport);
                                    },
                                    parameters: this.serialize(true)
                                });
                                
                            return false;
                        }
                    })
                    
                    $('coupon_code').observe('keypress', function(event){
                        var key = event.which || event.keyCode;
                        switch (key) {
                            default:
                            break;
                            case Event.KEY_RETURN:
                                discountForm.submit();
                                Event.stop(event);
                            break;   
                        }
                    });
                    
                    discountForm = new VarienForm('discount-coupon-form');
                    initDiscountForm();
                }
            }

            var container = $('discount-coupon-form');//.up();//$$('.discount')[0];

            moveForm(container);

//            _caller.showPleaseWait(true, container);
        }
        
    }
}

var amContentWin = null


var amShowDialog = function (){
      if (!amContentWin){
        amContentWin = new Window({
            draggable: true,
            resizable: true,
            closable: true,
            className: "alphacube",
            windowClassName: "popup-window",
            title: '',
            width: 480,
            height: 350,
            zIndex: 1000,
            recenterAuto: true,
            hideEffect: Element.hide,
            showEffect: Element.show,
            id: 'viewDialog'
        });
        
//        amContentWin = new Window({
//            draggable: true,
//            resizable: true,
//            closable: true,
//            className: "alphacube",
//            windowClassName: "popup-window",
//            title: '',
//            width: 480,
//            height: 350,
//            zIndex: 1000,
//            recenterAuto: true,
////            hideEffect: Element.hide,
////            showEffect: Element.show,
//            id: 'viewDialog'
//        })
        
        amContentWin.setContent('am_login');
        
    }
    amContentWin.showCenter(true);
    loginForm = new VarienForm('login-form', true);
}