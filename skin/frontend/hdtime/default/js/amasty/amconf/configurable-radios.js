
Product.Config.prototype.initialize = function(config){
    this.config     = config;
    this.taxConfig  = this.config.taxConfig;
    if (config.containerId) {
        this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select');
    } else {
        this.settings   = $$('.custom-radio-configurable');
    }
    this.state      = new Hash();
    this.priceTemplate = new Template(this.config.template);
    this.prices     = config.prices;

    // Set default values from config
    if (config.defaultValues) {
        this.values = config.defaultValues;
    }

    // Overwrite defaults by url
    var separatorIndex = window.location.href.indexOf('#');
    if (separatorIndex != -1) {
        var paramsStr = window.location.href.substr(separatorIndex+1);
        var urlValues = paramsStr.toQueryParams();
        if (!this.values) {
            this.values = {};
        }
        for (var i in urlValues) {
            this.values[i] = urlValues[i];
        }
    }

    // Overwrite defaults by inputs values if needed
    if (config.inputsInitialized) {
        this.values = {};
        this.settings.each(function(element) {
            if (element.value) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                this.values[attributeId] = element.value;
            }
        }.bind(this));
    }

    // Put events to check select reloads
    this.settings.each(function(element){
        Event.observe(element, 'change', this.configure.bind(this))
    }.bind(this));

    // fill state
    this.settings.each(function(element){
        var attributeId = element.id.replace(/[a-z]*/, '');
        if(attributeId && this.config.attributes[attributeId]) {
            element.config = this.config.attributes[attributeId];
            element.attributeId = attributeId;
            this.state[attributeId] = false;
        }
    }.bind(this))

    // Init settings dropdown
    var childSettings = [];
    for(var i=this.settings.length-1;i>=0;i--){
        var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
        var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
        if (i == 0){
            this.fillSelect(this.settings[i])
        } else {
            jQuery(this.settings[i]).hide();
            jQuery('#label' + this.settings[i].attributeId).hide();
        }
        $(this.settings[i]).childSettings = childSettings.clone();
        $(this.settings[i]).prevSetting   = prevSetting;
        $(this.settings[i]).nextSetting   = nextSetting;
        childSettings.push(this.settings[i]);
    }

    // Set values to inputs
    this.configureForValues();
    document.observe("dom:loaded", this.configureForValues.bind(this));
}

Product.Config.prototype.fillSelect = function(element, selectedValue){
    var attributeId = element.id.replace(/[a-z]*/, '');
    var options = this.getAttributeOptions(attributeId);
    this.clearSelect(element);
    //element.options[0] = new Option(this.config.chooseText, '');

    var prevConfig = false;
    if(element.prevSetting){
        //prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        //element.prevSetting.config.options
        prevConfig = this.findSelected(element, selectedValue);
    }

    if(options) {
        if ($('amconf-images-' + attributeId))
        {
            $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
        }

        if (this.config.attributes[attributeId].use_image)
        {
            holder = element.parentNode;
            holderDiv = document.createElement('div');
            holderDiv = $(holderDiv); // fix for IE
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId;
            holder.insertBefore(holderDiv, element);
        }
        // extension Code End

        var index = 1;
        for(var i=0;i<options.length;i++){
            var allowedProducts = [];
            if(prevConfig) {
                for(var j=0;j<options[i].products.length;j++){
                    if(prevConfig.allowedProducts.length > 0
                        && prevConfig.allowedProducts.indexOf(options[i].products[j])>-1){
                        allowedProducts.push(options[i].products[j]);
                    }
                }
            } else {
                allowedProducts = options[i].products.clone();
            }

            if(allowedProducts.size()>0)
            {
                // extension Code
                if (this.config.attributes[attributeId].use_image)
                {
                    var imgContainer = document.createElement('div');
                    imgContainer = $(imgContainer); // fix for IE
                    imgContainer.addClassName('amconf-image-container');
                    imgContainer.id = 'amconf-images-container-' + attributeId;
                    imgContainer.style.float = 'left';
                    holderDiv.appendChild(imgContainer);

                    var image = document.createElement('img');
                    image = $(image); // fix for IE
                    image.id = 'amconf-image-' + options[i].id;
                    image.src = options[i].image;
                    image.addClassName('amconf-image');
                    image.alt = options[i].label;
                    image.title = options[i].label;

                    if(showAttributeTitle != 0) image.style.marginBottom = '0px';
                    else image.style.marginBottom = '7px';

                    image.observe('click', this.configureImage.bind(this));

                    if('undefined' != typeof(buble)){
                        image.observe('mouseover', buble.showToolTip);
                        image.observe('mouseout', buble.hideToolTip);
                    }

                    imgContainer.appendChild(image);

                    if(showAttributeTitle && showAttributeTitle != 0){
                        var amImgTitle = document.createElement('div');
                        amImgTitle = $(amImgTitle); // fix for IE
                        amImgTitle.addClassName('amconf-image-title');
                        amImgTitle.id = 'amconf-images-title-' + options[i].id;
                        amImgTitle.setStyle({
                            fontWeight : 600,
                            textAlign : 'center'
                        });
                        amImgTitle.innerHTML = options[i].label;
                        imgContainer.appendChild(amImgTitle);
                    }
                    image.onload = function(){
                        var optId = this.id.replace(/[a-z-]*/, '');
                        var maxW = this.getWidth();
                        if(optId) {
                            var title = $('amconf-images-title-' + optId);
                            if(title && title.getWidth() && title.getWidth() > maxW) {
                                maxW = title.getWidth();
                            }

                        }
                        if(this.parentNode){
                            this.parentNode.style.width =   maxW + 'px';
                        }
                        if(this.parentNode.childElements()[1]){
                            this.parentNode.childElements()[1].style.width =   maxW + 'px';
                        }
                    };
                }
                // extension Code End

                options[i].allowedProducts = allowedProducts;
                var template = '<label class="label-radio-configurable" id="">' +
                    '<input type="radio" name="super_attribute[{attributeId}]" id="attribute{secondAttributeId}" class="validate-custom-configurable" value="{optionId}"/>' +
                    '<span class="mark"></span>' +
                    '{label}' +
                '</label>';

                var itemTxt = template.replace('{label}', options[i].label)
                    .replace('{optionId}', options[i].id)
                    .replace('{attributeId}', attributeId)
                    .replace('{secondAttributeId}', attributeId)
                    .replace('{price}', options[i].price);

                jQuery('#' + options[i].id).html(itemTxt);


                var customRadio = jQuery('#' + options[i].id).parent();
                customRadio.show();
                customRadio.prev().show();

                //element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                //element.options[index].config = options[i];
                if(index>3){
                    jQuery('#' + options[i].id).hide();
                    jQuery('#' + options[i].id).parent().find('.show-more').show();
                    jQuery('#' + options[i].id).parent().find('.show-less').hide();
                } else {

                    jQuery('#' + options[i].id).parent().find('.show-more').hide();
   
				}
                index++;
            }
            jQuery('#' + options[i].id).parent().find('.count').html(index-4);

        }

        if(this.config.attributes[attributeId].use_image) {
            var lastContainer = document.createElement('div');
            lastContainer = $(lastContainer); // fix for IE
            lastContainer.setStyle({clear : 'both'});
            holderDiv.appendChild(lastContainer);
        }
    }
}

Product.Config.prototype.clearSelect = function(element){
    jQuery(element).find('.radio-item').html('');
}

Product.Config.prototype.findSelected = function(element, selectedValue){
   if (element.prevSetting) {
      return jQuery.grep(element.prevSetting.config.options, function(e){ return e.id == selectedValue; })[0];
   } else {
       return element;
   }
}

Product.Config.prototype.configureElement = function(element)
{
    // extension Code
    optionId = element.value;
    if ($('amconf-image-' + optionId))
    {
        this.selectImage($('amconf-image-' + optionId));
    } else
    {
        attributeId = element.id.replace(/[a-z-]*/, '');
        if ($('amconf-images-' + attributeId))
        {
            $('amconf-images-' + attributeId).childElements().each(function(child){
                if(child.childElements()[0])
                    child.childElements()[0].removeClassName('amconf-image-selected');
            });
        }
    }
    // extension Code End

    //this.reloadOptionLabels(element);
    if(element.value){
        //this.state[element.config.id] = element.value;
        var parent = jQuery(element).parent().parent().parent('.custom-radio-configurable');

        // clean selected
        parent.find('.validate-custom-configurable').removeAttr('selected');
        jQuery(element).attr('selected', 'selected');

        var el = parent.get(0);
        if(el.nextSetting){
            el.nextSetting.disabled = false;
            this.fillSelect(el.nextSetting, element.value);
            this.resetChildren(el.nextSetting);
        }
    }
    else {
        // extension Code
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                attributeId = element.childSettings[i].id.replace(/[a-z-]*/, '');
                if ($('amconf-images-' + attributeId))
                {
                    $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
                }
            }
        }
        // extension Code End

        this.resetChildren(element);

        // extension Code
        if (this.settings[0].hasClassName('no-display'))
        {
            this.processEmpty();
        }
        // extension Code End
    }

    // extension Code
    var key = '';
    /*
    this.settings.each(function(select, ch){
        // will check if we need to reload product information when the first attribute selected
        if (parseInt(select.value))
        {
            key += select.value + ',';
        }
    });
    if (typeof confData != 'undefined') {
        confData.isResetButton = false;
    }
    */

    jQuery('.label-radio-configurable input[selected=selected]').each(function(idx, el) {
        key += jQuery(el).attr('value') + ',';
    });

    key = key.substr(0, key.length - 1);

    this.updateData(key);

    if (typeof confData != 'undefined' && confData.useSimplePrice == "1")
    {
        // replace price values with the selected simple product price
        this.reloadSimplePrice(key);
    }
    else
    {
        var selectedItem = this.findSelected(el.nextSetting, element.value);
        // default behaviour
        this.reloadPrice(selectedItem);
    }
    return;

    // for compatibility with custom stock status extension:
    if ('undefined' != typeof(stStatus) && 'function' == typeof(stStatus.onConfigure))
    {
        var key = '';
        this.settings.each(function(select, ch){
            if (parseInt(select.value) || (!select.value && (!select.options[1] || !select.options[1].value))){
                key += select.value + ',';
            }
            else {
                key += select.options[1].value + ',';
            }
        });
        key = key.substr(0, key.length - 1);
        stStatus.onConfigure(key, this.settings);
    }
    //Amasty code for Automatically select attributes that have one single value
    if(('undefined' != typeof(amConfAutoSelectAttribute) && amConfAutoSelectAttribute) ||('undefined' != typeof(amStAutoSelectAttribute) && amStAutoSelectAttribute)){
        var nextSet = element.nextSetting;
        if(nextSet && nextSet.options.length == 2 && !nextSet.options[1].selected && element && !element.options[0].selected){
            nextSet.options[1].selected = true;
            this.configureElement(nextSet);
        }
    }
    if('undefined' != typeof(preorderState))
        preorderState.update()


    var label = "";
    element.config.options.each(function(option){
        if(option.id == element.value) label = option.label;
    });
    if(label) label = " - " + label;
    var parent = element.parentNode.parentNode.previousElementSibling;
    if( typeof(parent) != 'undefined' && parent.nodeName == "DT" && (conteiner = parent.select("label")[0])) {
        if( tmp = conteiner.select('span.amconf-label')[0]){
            tmp.innerHTML = label;
        }
        else{
            var tmp = document.createElement('span');
            tmp.addClassName('amconf-label');
            conteiner.appendChild(tmp);
            tmp.innerHTML = label;
        }
    }
    // extension Code End
}

Product.Config.prototype.reloadPrice = function(selected){
    if (this.config.disablePriceReload) {
        return;
    }
    var price    = 0;
    var oldPrice = 0;

    price    += parseFloat(selected.price);
    oldPrice += parseFloat(selected.oldPrice);

    optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
    optionsPrice.reload();

    return price;
    if($('product-price-'+this.config.productId)){
        $('product-price-'+this.config.productId).innerHTML = price;
    }
    this.reloadOldPrice();
}