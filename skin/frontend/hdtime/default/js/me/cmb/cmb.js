/**
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */
var CmbRequest = Class.create();

CmbRequest.prototype = {
    initialize: function(form, cmbValidator, options){
        this.options = options || '';
        this.options.blockId = this.options.blockId || 'me-cmb';
        this.options.blockResponseId = this.options.blockResponseId || 'me-cmb-response';
        this.options.blockSubtitleId = this.options.blockSubtitleId || 'me-cmb-subtitle';
        this.options.delayTime = this.options.delayTime * 1000 || 10000;
        this.form = form;
        if ($(this.form)) {
            $('cmb-validate-detail').observe('submit', function(event){
                Event.stop(event);
                if(cmbValidator.validator && cmbValidator.validator.validate()){
                    this.sendAjax();
                }
            }.bind(this));
        }
    },
    sendAjax: function(){
        this.showLoader();
        var request = new Ajax.Request(
            this.options.ajaxUrl,
            {method:'post', parameters: Form.serialize(this.form), onSuccess: this.onUpdate.bind(this), onFailure: this.ajaxFailure.bind(this)}
        );
    },
    onUpdate: function(transport){
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        if (response.success){
            this.hideLoader();
            this.showSuccessMsg(response.html);
			
			jQuery('#cmb-validate-detail ul').remove();
			jQuery('#cmb-validate-detail .buttons-set').remove();
            return true;
        } else {
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                alert(response.message.join("\n"));
            }
            this.hideLoader();
            this.reloadError();
        }
    },
    ajaxFailure: function(transport){
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            alert(response.message.join("\n"));
        }
        this.hideLoader();
        this.reloadError();
    },
    reloadError: function()
    {
        location.reload();
    },
    showLoader: function() {
        $(this.options.blockId).addClassName('loading');
    },
    hideLoader: function() {
        $(this.options.blockId).removeClassName('loading');
    },
    showSuccessMsg: function(successContent) {
        $(this.form).reset();
        if ($(this.options.blockSubtitleId) != undefined) {
            $(this.options.blockSubtitleId).hide();
        }
        $(this.options.blockResponseId).show();
        $(this.options.blockResponseId).update(successContent);
        setTimeout(function(){
            $(this.options.blockResponseId).hide();
            $(this.options.blockResponseId).update('');
            if ($(this.options.blockSubtitleId) != undefined) {
                $(this.options.blockSubtitleId).show();
            }
        }, this.options.delayTime);
    }
};
