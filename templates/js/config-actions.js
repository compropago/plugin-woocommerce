/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

const modal = $('.cp-modal');

window.onclick = function(event) {
    const aux = document.querySelector('.cp-modal');
    if (event.target === aux) {
        modal.fadeOut();
    }
};

$(function(){
    $("#agregar_proveedor").click(function(){
        $("#prov-disabled option").each(function(){
            console.log($(this).val());
            if($(this).is(':selected')){
                $('#prov-allowed').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
                $(this).remove();
            }
        });
    });

    $('#quitar_proveedor').click(function(){
        $('#prov-allowed option').each(function(){
            if($(this).is(':selected')){
                $('#prov-disabled').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
                $(this).remove();
            }
        });
    });

    $("#save-config-compropago").click(function(){
        const regexp = /^pk_/;
        const publickey = $('#publickey');
        const privatekey = $('#privatekey');

        if (!regexp.test(publickey.val()) && !regexp.test(privatekey.val())) {
            modal.fadeIn();
            return true;
        }

        saveConfig();
    });

    $('#save-all').click(function() {
        const live = $('#live').is(':checked') ? 'yes' : 'no';
        saveConfig(live);
    });
});

function saveConfig(live = 'no') {
    const loadImage = $('#loading');
    const errorDisplay = $('#display_error_config');

    loadImage.fadeIn();

    const data = {
        cash_enable:    $('#enable_cash').is(':checked') ? 'yes' : 'no',
        spei_enable:    $('#enable_spei').is(':checked') ? 'yes' : 'no',
        cash_title:     $('#cash_title').val(),
        spei_title:     $('#spei_title').val(),
        publickey:      $('#publickey').val(),
        privatekey:     $('#privatekey').val(),
        webhook:        $('#webhook').val(),
        provallowed:    getProvidersAllowed(),
        complete_order: $('#complete_order').val(),
        initial_state:  $('#intial_state').val(),
        debug:          $('#debug').is(':checked') ? 'yes' : 'no',
    };

    if (live !== 'no') {
        data.live = live;
    }

    if(validateSendConfig(data)){
        $.ajax({
            url: $('#url-save').val(),
            type: 'post',
            data: data,
            success: function(json) {
                if (json.error) {
                    console.log('error');
                    errorDisplay.removeClass('notice-success');
                    errorDisplay.addClass('error');
                } else {
                    errorDisplay.removeClass('error');
                    errorDisplay.addClass('notice-success');
                }

                errorDisplay.html(json.message);
                errorDisplay.fadeIn();

                $(document).scrollTop($("body").offset().top);
            },
            error: function(res){
                const json = res.responseJSON;

                console.log(json);

                errorDisplay.removeClass('notice-success');
                errorDisplay.addClass('error');
                errorDisplay.html(json.message);
                errorDisplay.fadeIn();
                $(document).scrollTop($("body").offset().top);
            }
        });
    }

    window.setTimeout(function(){
        errorDisplay.fadeOut();
    },10000);

    loadImage.fadeOut();
    modal.fadeOut();
}

/**
 * Get allow providers
 * @return {string}
 */
function getProvidersAllowed(){
    var active = '';

    $('#prov-allowed option').each(function(){
        if(active === ''){
            active += $(this).val();
        }else{
            active += ','+$(this).val();
        }
    });

    if(active === ''){
        $('#prov-disabled option').each(function(){
            if(active === ''){
                active += $(this).val();
            }else{
                active += ','+$(this).val();
            }
        });
    }

    return active;
}

/**
 * Validate Compropago Keys not empty
 * @param {Object} data
 * @return {boolean}
 */
function validateSendConfig(data){
    if (data.publickey.length === 0 || data.privatekey.length === 0) {
        alert('Las llaves no deben de estar vacias');
        return false;
    }

    return true;
}

/**
 * Open a tab menu
 * @param {Object} evt 
 * @param {string} tabName 
 */
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("nav-tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " nav-tab-active";
}

