/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_ProductBaseCurrency
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Base Currency Control
 */
var BaseCurrencyControl = Class.create(FormElementControl, {

    initialize : function($super, data) {
        if (!data) data = {};
        if (data.websiteId) {
            this.websiteId                  = data.websiteId;
        }
        if (data.defaultBaseCurrencyCode) {
            this.defaultBaseCurrencyCode    = data.defaultBaseCurrencyCode;
        }
        if (data.websiteBaseCurrencyCodes) {
            this.websiteBaseCurrencyCodes    = data.websiteBaseCurrencyCodes;
        }
        if (data.priceElementId) {
            this.priceElementId             = data.priceElementId;
        }
        if (data.specialPriceElementId) {
            this.specialPriceElementId      = data.specialPriceElementId;
        }
        if (data.groupPriceElementId) {
            this.groupPriceElementId        = data.groupPriceElementId;
        }
        if (data.tierPriceElementId) {
            this.tierPriceElementId         = data.tierPriceElementId;
        }
        $super(data);
        var self = this;
        this.getBaseCurrencyElement().observe('change', function () { self.render(); });
        if (this.hasAddGroupPriceButton()) {
            this.getAddGroupPriceButton().observe('click', function () { self.render(); });
        }
        if (this.hasAddTierPriceButton()) {
            this.getAddTierPriceButton().observe('click', function () { self.render(); });
        }
    }, 
    
    getWebsiteId : function() {
        return this.websiteId;
    }, 
    
    getBaseCurrencyElement : function() {
        return $(this.getElement().select('select').first());
    }, 
            
    getDefaultBaseCurrencyCode : function() {
        return this.defaultBaseCurrencyCode;
    }, 
            
    getWebsiteBaseCurrencyCode : function(websiteId) {
        if (
            this.websiteBaseCurrencyCodes && 
            (this.websiteBaseCurrencyCodes instanceof Object) && 
            (websiteId in this.websiteBaseCurrencyCodes)
        ) {
            return this.websiteBaseCurrencyCodes[websiteId];
        }
        return null;
    }, 
            
    getBaseCurrencyCode : function() {
        return this.getBaseCurrencyElement().getValue();
    }, 
    
    getBaseCurrencyText : function(websiteId) {
        var currency = this.getBaseCurrencyCode();
        if (!currency && websiteId) {
            currency = this.getWebsiteBaseCurrencyCode(websiteId);
        }
        if (!currency) {
            currency = this.getDefaultBaseCurrencyCode();
        }
        return '[' + currency + ']';
    }, 
    
    replaceBaseCurrencyText : function(el, websiteId) {
        el.update(el.innerHTML.replace(/\[(.*?)\]/g, this.getBaseCurrencyText(websiteId)));
    }, 
    
    _updatePrice : function(el) {
        if (el) {
            this.replaceBaseCurrencyText(el.next('strong'), null);
        }
    }, 
    
    _updateGroupPrice : function(el) {
        if (el) {
            el.select('tr').each(function(tr) {
                $(tr).select('td').each(function(td) {
                    var websiteId = null;
                    $(td).select('select').each(function(select) {
                        var selectEl = $(select);
                        var selectName = selectEl.readAttribute('name');
                        if (selectName.include('website_id')) {
                            selectEl.select('option').each(function(option) {
                                var optionEl = $(option);
                                websiteId = optionEl.readAttribute('value');
                                if (
                                    !this.getWebsiteId() || 
                                    (this.getWebsiteId() && (this.getWebsiteId() === websiteId))
                                ) {
                                    this.replaceBaseCurrencyText(optionEl, websiteId);
                                }
                            }, this);
                        }
                    }, this);
                    $(td).select('.website-name').each(function(span) {
                        var spanEl = $(span);
                        if (
                            !this.getWebsiteId() || 
                            (this.getWebsiteId() && (this.getWebsiteId() === websiteId))
                        ) {
                            this.replaceBaseCurrencyText(spanEl, websiteId);
                        }
                    }, this);
                }, this);
            }, this);
        }
    }, 
    
    getPriceElement : function() {
        if (this.priceElementId) {
            return $(this.priceElementId);
        }
        return null;
    }, 
    
    hasPriceElement : function() {
        return (this.getPriceElement()) ? true : false;
    }, 
            
    updatePrice : function() {
        this._updatePrice(this.getPriceElement());
    }, 
            
    getSpecialPriceElement : function() {
        if (this.specialPriceElementId) {
            return $(this.specialPriceElementId);
        }
        return null;
    }, 
    
    hasSpecialPriceElement : function() {
        return (this.getSpecialPriceElement()) ? true : false;
    }, 
    
    updateSpecialPrice : function() {
        this._updatePrice(this.getSpecialPriceElement());
    }, 
            
    getGroupPriceContainerElement : function() {
        if (this.groupPriceElementId) {
            return $(this.groupPriceElementId + '_container');
        }
        return null;
    }, 
    
    hasGroupPriceContainerElement : function() {
        return (this.getGroupPriceContainerElement()) ? true : false;
    }, 
            
    getAddGroupPriceButton : function() {
        if (this.hasGroupPriceContainerElement) {
            return $(this.getGroupPriceContainerElement().next('tfoot').select('button').first());
        }
        return null;
    }, 
    
    hasAddGroupPriceButton : function() {
        return (this.getAddGroupPriceButton()) ? true : false;
    }, 
    
    updateGroupPrice : function() {
        this._updateGroupPrice(this.getGroupPriceContainerElement());
    }, 
    
    getTierPriceContainerElement : function() {
        if (this.tierPriceElementId) {
            return $(this.tierPriceElementId + '_container');
        }
        return null;
    }, 
    
    hasTierPriceContainerElement : function() {
        return (this.getTierPriceContainerElement()) ? true : false;
    }, 
            
    getAddTierPriceButton : function() {
        if (this.hasTierPriceContainerElement) {
            return $(this.getTierPriceContainerElement().next('tfoot').select('button').first());
        }
        return null;
    }, 
    
    hasAddTierPriceButton : function() {
        return (this.getAddTierPriceButton()) ? true : false;
    }, 
    
    updateTierPrice : function() {
        this._updateGroupPrice(this.getTierPriceContainerElement());
    }, 
    
    render : function() {
        this.updatePrice();
        this.updateSpecialPrice();
        this.updateGroupPrice();
        this.updateTierPrice();
    }
});