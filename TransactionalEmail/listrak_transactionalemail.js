var _form_html_row = '';
var attributeDropDown = '';
jQuery(document).ready(function() {
    jQuery('#store_id option[value=0]').text('Select a store view');
    jQuery('#store_id option[value=0]').val('');
    getStoreView();
    addOption();
});
var getListrakAttributes = function(mode) {
    errorMessageClear();
    var request = jQuery.ajax({
        url: ajax_attribute_url,
        type: "POST",
        data: {
            store_id: jQuery('#store_id option:selected').val(),
            form_key: jQuery("input[name='form_key']").val()
        },
        dataType: "json",
    });
    request.done(function(object) {
        if (object.error) {
            errorMessageShow(object.error, 'error');
            fetchModeDomElements(true);
        } else {
            attributeDropDown = object.data;
            if (mode == 'add') {
                _form_html_row = htmlMultipleInput();
                jQuery('#attribute-options-table').append(_form_html_row.replace(/\{\{id\}\}/ig, _urls_counter));
        _urls_counter++;
              
            } else {
                //edit  mode
                jQuery('select[class=listrakAttributes]').each(function() {
                    jQuery(this).append(attributeDropDown);
                    selectedValue = jQuery(this).data('selected');
                    jQuery(this).find('option[value="' + selectedValue + '"]').attr("selected", "selected");
                });
            }
            fetchModeDomElements(false);
        }
        jQuery("#loading-mask").hide();
    });
    request.fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
var getStoreView = function() {
    jQuery('#store_id').on('change', function() {
        if (this.value > 0 && jQuery('#attribute_store_id').val() != this.value) {
            jQuery('#attribute_store_id').val(this.value)
            jQuery("#loading-mask").show();
            jQuery("#store_view").html(jQuery('#store_id option:selected').text());
            jQuery("#store_view").removeClass('error');
            getListrakAttributes('add');
            jQuery('.option-row').remove();
        }
    });
    //On edit mode 
    jQuery('#transactionalmessages_tabs_attribute_fields').click(function() {
        if (jQuery('#store_id option:selected').val() > 0 && jQuery('#attribute_store_id').val() != jQuery('#store_id option:selected').val()) {
            jQuery('#attribute_store_id').val(jQuery('#store_id option:selected').val())
            jQuery("#loading-mask").show();
            jQuery("#store_view").html(jQuery('#store_id option:selected').text());
            jQuery("#store_view").removeClass('error');
            getListrakAttributes('edit');
        }
    });
}
var errorMessageShow = function(msg, msgType) {
    jQuery("#messages").html('<ul class="messages"><li class="' + msgType + '-msg"><ul><li><span>' + msg + '</span></li></ul></li></ul>');
}
var errorMessageClear = function() {
    jQuery("#messages").html('');
}
var addOption = function() {
    jQuery('#add_new_option_button').click(function() {
        var _form_html_row = htmlMultipleInput();
        jQuery('#attribute-options-table').append(_form_html_row.replace(/\{\{id\}\}/ig, _urls_counter));
        _urls_counter++;
    });
}
var htmlMultipleInput = function() {
    var _form_html_row = '<tr class="option-row" id="multiple-row-{{id}}">';
    _form_html_row += '<td><input name="mulitpleField[value_{{id}}][mapfield]" value="" class="input-text required-option" type="text"></td>';
    _form_html_row += '<td><select name="mulitpleField[value_{{id}}][attribute]"   class="listrakAttributes"><option>Select an attribute</option>' + attributeDropDown + '<select>';
    _form_html_row += '<input type="hidden" name="mulitpleField[value_{{id}}][attribute][elements]" value="" ></td>';
    _form_html_row += '<td class="a-left" id="delete_button_container_option_{{id}}">';
    _form_html_row += '<input id="delete-row-{{id}}" type="hidden" class="delete-flag" name="mulitpleField[value_{{id}}][delete]" value=""/>';
    _form_html_row += '<button title="Delete" type="button" class="scalable delete delete-option" data-id="{{id}}"><span><span><span>Delete</span></span></span></button>';
    _form_html_row += '</td></tr>';
    return _form_html_row;
}
var fetchModeDomElements = function(type) {
    if (type == false) {
        jQuery('#add_new_option_button').removeClass('disabled');
        jQuery('#add_new_option_button').addClass('add');
        jQuery('#add_new_option_button').attr('disabled', false);
    } else {
        jQuery('#add_new_option_button').removeClass('add');
        jQuery('#add_new_option_button').addClass('disabled');
        jQuery('#add_new_option_button').attr('disabled', true);
        jQuery('.option-row').remove();
    }
}
jQuery(document).on('click', '.delete', function() {
    var editid = jQuery(this).data('editid');
    var id = jQuery(this).data('id'); 
    if (editid) {
         var result = confirm("Are you sure to delete this item?");
         if(result)
            jQuery('#multiple-row-' + id).remove();
    } else {
        jQuery('#multiple-row-' + id).remove();
    }
});
jQuery(document).on('change', '.listrakAttributes', function() {
    nameElement = jQuery(this).attr('name') + "[elements]";
    var dataValue = jQuery(this).find(':selected').data();
    jQuery("input[name='" + nameElement + "']").val(JSON.stringify(dataValue));
});