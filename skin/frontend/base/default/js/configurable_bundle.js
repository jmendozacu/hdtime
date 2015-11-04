/**
 * Fill the next attribute of the current option and remove anything left over that should not be there
 * @param element
 * @param current_option_id
 * @param current_attribute_id
 */
function fillNextAttribute(element, option_id, attribute_id)
{
    if(typeof baseUrl == 'undefined') {
        var baseUrl = '';
    }

    if(getConfigurableId(option_id) == 0) {
        return;
    }

    setCheckoutDisabled();

    new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/nextattributeid/id/' + getConfigurableId(option_id) + '/attribute_id/' + attribute_id, {
        method:'get',
        onSuccess: function(transport) {            
            if(transport.responseText != '') {
                var next_attribute_id = transport.responseText;
               
                new Ajax.Request(baseUrl + '/wizbundle/ajax/singleattribute/id/' + getConfigurableId(option_id) + '/option_id/' + option_id + '/attribute_id/' + next_attribute_id, {
                    method: 'POST',
                    postBody: Object.toQueryString({attributes: Object.toJSON(getOptionValues(option_id, attribute_id))}),
                    onSuccess: function(transport) {
                        // Only update if we got a unique result
                        var selector = '.option-' + option_id + '-attribute-'+ next_attribute_id;
                        // Remove old label, replace old element for new code
                        $$('dt' + selector)[0].remove();
                        $$('dd' + selector)[0].replace(transport.responseText);
                    }
                });
            }else{
                setCheckoutDisabled();
                var custom_options_id = 'custom-option-'+ option_id +'-'+ attribute_id;
                if($(custom_options_id)){
                    $(custom_options_id).replace('');
                }
                new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/productoptions/id/' + getConfigurableId(option_id), {
                method: 'POST',
                postBody: Object.toQueryString({bundle_id: bundle.config.bundleId, option_id: option_id, attributes: Object.toJSON(getOptionValues(option_id, attribute_id))}),
                onSuccess: function(transport) {
                    if(transport.responseText != ''){
                        var selector = '.option-' + option_id + '-attribute-'+ attribute_id;  
                        if(!$(custom_options_id)){
                            $$('dd' + selector)[0].insert({after: '<div id="' + custom_options_id + '">'+transport.responseText + '</div>'});                                               
                        }else{
                            $(custom_options_id).replace(transport.responseText);
                        }
                    }
                    unsetCheckoutDisabled();
                }
            });
            }            
        },
        onFailure: function() { }
    });

    // Update the real input field
    updateRealInputField(option_id, attribute_id);
}

/**
 * When the dom is loaded, fill the first attribute
 */
document.observe("dom:loaded", function() {
    $H(bundle.config.options).each(function(pair) {
        fillFirstAttribute(pair.key);
    });
});

/**
 * After changing the configurable, update its image
 */
function updateConfigurableData(option_id)
{
    if(typeof baseUrl == 'undefined') {
        var baseUrl = '';
    }

    if(getConfigurableId(option_id) == 0) {
        return;
    }

    new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/realproductinfo/product_id/' + getConfigurableId(option_id), {
         method:'get',
         onSuccess: function(transport) {
             if(transport.responseText != '') {
                 var data = JSON.parse(transport.responseText);
             }
        
            if(updateStatus.configurable_image !=  0) {
                $('bundle-option-image-' + option_id).setAttribute('src', data.image);
            }
	    
            unsetCheckoutDisabled();
         },
         onFailure: function() { }
     });
}

/**
 * After a unique simple product has been selected, update its information on the page
 */
function updateProductInfo(product_id, option_id)
{
    if(typeof baseUrl == 'undefined') {
        var baseUrl = '';
    }

    if(getConfigurableId(option_id) == 0) {
        return;
    }

    new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/productinfo/selection_id/' + product_id + '/bundle_id/' + bundle.config.bundleId, {
        method:'get',
        onSuccess: function(transport) {
            if(transport.responseText != '') {
                var data = JSON.parse(transport.responseText);
            }
       

            if(updateStatus.image !=  0) {
                $('bundle-option-image-' + option_id).setAttribute('src', data.image);
            }
 

            if(updateStatus.name !=  0) {
                $('bundle-option-name-' + option_id).update(data.name);
            }
 
            if(updateStatus.description !=  0) {
                $('bundle-option-description-' + option_id).update(data.description);
            }

            if(updateStatus.stock !=  0) {
                var stockElement = new Element('p', {'class': 'availability'});
                if(data.stock > 0) {
                    $('oos-warning-' + option_id).addClassName('hidden');
                    stockElement.addClassName('in-stock');
                    stockElement.update('Availability: <span>In Stock</span>');
                } else {
                    $('oos-warning-' + option_id).removeClassName('hidden');
                    stockElement.addClassName('out-of-stock');
                    stockElement.update('Availability: <span>Out of Stock</span>');
                }
                $('bundle-option-stock-' + option_id).update(stockElement);
            }
            
            unsetCheckoutDisabled();
          },
          onFailure: function() { }
    });
}

/**
 * Get the configurable ID if that exists
 */
function getConfigurableId(option_id)
{
    if($('configurables-' + option_id + '-select')) {
        return $('configurables-' + option_id + '-select').getValue();
    }

    return 0;
}

/**
 * Disable the checkout button
 */
function setCheckoutDisabled()
{
    $$('.btn-cart').each(function(item,index){
        $(item).setAttribute('disabled', 'disabled');
	    $(item).addClassName('loading');
    }); 
}

/**
 * Enable the checkout button
 */
function unsetCheckoutDisabled()
{
    $$('.btn-cart').each(function(item,index){
        if($(item).hasAttribute('disabled')) {
            $(item).removeAttribute('disabled');
        }

        if($(item).hasClassName('loading')) {
            $(item).removeClassName('loading');
        }
    }); 
}

/**
 * After we changed the configurable, fill the first attribute
 */
function fillFirstAttribute(option_id)
{
    if(getConfigurableId(option_id) == 0) {
        return;
    }
    
    setCheckoutDisabled();
 
    loadConfigurableAttributes(getConfigurableId(option_id), option_id);

    updateConfigurableData(option_id);
}

/**
 * Load all the attributes that the new configurable we selected has
 */
function loadConfigurableAttributes(configurable_id, current_option_id)
{
    if(typeof baseUrl == 'undefined') {
        var baseUrl = '';
    }

    if(configurable_id == 0) {
        return;
    }

    new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/attributes/id/' + configurable_id + '/option_id/' + current_option_id, {
        method:'get',
        onSuccess: function(transport) {
            $('bundle-options-' + current_option_id).select('dt').each(function(el){
                $(el).remove();
            });

            $('bundle-options-' + current_option_id).select('dd').each(function(el){
                $(el).remove();
            });

            $('bundle-options-' + current_option_id).insert(transport.responseText);

            unsetCheckoutDisabled();
        },
        onFailure: function() { }
    });
}

/**
 * After changing the selections, update the real update field
 *
 * @param option_id
 */
function updateRealInputField(option_id, attribute_id)
{
    if(typeof baseUrl == 'undefined') {
        var baseUrl = '';
    }

    if(getConfigurableId(option_id) == 0) {
        return;
    }

    new Ajax.Request(baseUrl + '/index.php/wizbundle/ajax/realproducts/id/' + getConfigurableId(option_id), {
        method: 'POST',
        postBody: Object.toQueryString({bundle_id: bundle.config.bundleId, option_id: option_id, attributes: Object.toJSON(getOptionValues(option_id, attribute_id))}),
        onSuccess: function(transport) {
            if(transport.responseText != '') {
                var data = JSON.parse(transport.responseText);
            }
         
            // Only update if we got a unique result
            if(data.length == 1) {                      
                $('bundle-option-' + option_id + '-select').value = data[0];
                triggerEvent($('bundle-option-'+option_id+'-select'), 'change');

                // Update the product information in the screen
                updateProductInfo(data[0], option_id);
            }
        }
    });
}

/**
 * Get all the option values available in the selector
 *
 * @param option_id
 * @returns {{}}
 */
function getOptionValues(option_id, attribute_id)
{
    var selector = $('bundle-options-' + option_id).select('select');

    if($(selector) == undefined) {
        var selector = $('bundle-options-' + option_id).select('input:checked');
    }

    var values = {};

    $H(selector).each(function(item, index) {
        // If the first key is a numeric value
        if($(item)[0].match(/^\d+$/) != null) {
            var select = $(item)[1];

            var current_attribute_id = resolveAttributeId($(select).getAttribute('name'), option_id);

            if(typeof values[current_attribute_id] == 'undefined') {
                values[current_attribute_id] = [];
            }

            values[current_attribute_id].push($(select).getValue());

            // Dont continue, we break since we just changed this one
            if(current_attribute_id == attribute_id) {
                throw $break;
            }
        }

    });

    return values;
}

/**
 * Resolve an attribute ID from a select element
 */
function resolveAttributeId(name, option_id)
{
    // Strip all in front of the attribute_id
    var attribute_id = name.replace("select_super_attribute\[" + option_id + "\]\[", "");

    // Strip all after, so we only keep the attribute itd itself
    return attribute_id.replace("\]", "");

}

/**
 * this supports trigger native events such as 'onchange' 
 * whereas prototype.js Event.fire only supports custom events
 */
function triggerEvent(element, eventName) {
    // safari, webkit, gecko
    if (document.createEvent)
    {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent(eventName, true, true);
 
        return element.dispatchEvent(evt);
    }
 
    // Internet Explorer
    if (element.fireEvent) {
        return element.fireEvent('on' + eventName);
    }
}
