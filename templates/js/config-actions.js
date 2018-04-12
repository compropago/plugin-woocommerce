/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
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
        $("#loadig").fadeIn();

        var data = {
            cash_enable:    $('#enable_cash').is(':checked') ? 'yes' : 'no',
            spei_enable:    $('#enable_spei').is(':checked') ? 'yes' : 'no',
            cash_title:     $('#cash_title').val(),
            spei_title:     $('#spei_title').val(),
            publickey:      $('#publickey').val(),
            privatekey:     $('#privatekey').val(),
            live:           $('#live').is(':checked') ? 'yes' : 'no',
            webhook:        $('#webhook').val(),
            provallowed:    getProvidersAllowed(),
            complete_order: $('#complete_order').val(),
            initial_state:  $('#intial_state').val(),
            debug:          $('#debug').is(':checked') ? 'yes' : 'no',
        };

        if(validateSendConfig(data)){
            $.ajax({
                url: '../wp-content/plugins/compropago/controllers/ConfigController.php',
                type: 'post',
                data: data,
                success: function(res){
                    if(res.error){
                        $("#display_error_config").removeClass('update');
                        $("#display_error_config").addClass('error');
                    }else{
                        $("#display_error_config").removeClass('error');
                        $("#display_error_config").addClass('notice-success');
                    }

                    $(document).scrollTop($("body").offset().top);
                    $("#display_error_config").html(res.message);
                    $("#display_error_config").fadeIn();
                    $("#display_error_config").removeClass('error');

                    renderRetro(res.retro);
                },
                error: function(res){
                    $("#display_error_config").removeClass('error');
                    $("#display_error_config").addClass('update');
                    $(document).scrollTop($("body").offset().top);
                    $("#display_error_config").html(res);
                    $("#display_error_config").fadeIn();
                    $("#display_error_config").removeClass('error');

                    console.log(res);
                }
            });
        }

        timer = window.setTimeout(function(){
            $("#display_error_config").fadeOut();
        },10000);

        $("#loadig").fadeOut();
    });

});

/**
 * Get allow providers
 * @return {string}
 */
function getProvidersAllowed(){
    active = '';

    $('#prov-allowed option').each(function(){
        if(active == ''){
            active += $(this).val();
        }else{
            active += ','+$(this).val();
        }
    });

    if(active == ''){
        $('#prov-disabled option').each(function(){
            if(active == ''){
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
 * @param data
 * @return {boolean}
 */
function validateSendConfig(data){
    if (data.publickey.length == 0 || data.privatekey.length == 0) {
        alert('Las llaves no deben de estar vacias');
        return false;
    }

    return true;
}

/**
 * Display retro info
 * @param {any} retro 
 */
function renderRetro(retro){
    if (retro != null){
        if(retro[0]){
            $("#retro").html(retro[1]);
            $("#retro").css('display', 'block');
        }else{
            $("#retro").css('display', 'none');
        }
    }
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
