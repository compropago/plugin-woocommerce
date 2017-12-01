/**
 * Created by Arthur on 22/07/16.
 */
$(function(){
    $("#agregar_proveedor").click(function(){
        $("#prov-disabled option").each(function(){
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

        data = {
            enabled:        $('#enabled').is(':checked') ? 'yes' : 'no',
            publickey:      $('#publickey').val(),
            privatekey:     $('#privatekey').val(),
            live:           $('#live').is(':checked') ? 'yes' : 'no',
            showlogo:       $('#showlogo').is(':checked') ? 'yes' : 'no',
            webhook:        $('#webhook').val(),
            provallowed:    getProvidersAllowed(),
            descripcion:    $('#descripsion').val(),
            instrucciones:  $('#instrucciones').val(),
            title:          $('#title').val(),
            complete_order: $('#complete_order').val(),
            initial_state:  $('#intial_state').val(),
            debug:          $('#debug').is(':checked') ? 'yes' : 'no',
        };

        console.log(data);

        if(validateSendConfig(data)){
            $.ajax({
                url: '../wp-content/plugins/compropago/controllers/ConfigController.php',
                type: 'post',
                data: data,
                success: function(res){
                    console.log(res);
                    if(res.error){
                        $("#display_error_config").removeClass('cpsuccess');
                        $("#display_error_config").addClass('cperror');
                    }else{
                        $("#display_error_config").removeClass('cperror');
                        $("#display_error_config").addClass('cpsuccess');
                    }

                    $(document).scrollTop($("body").offset().top);
                    $("#display_error_config").html(res.message);
                    $("#display_error_config").fadeIn();
                    $("#display_error_config").removeClass('error');

                    renderRetro(res.retro);
                },
                error: function(res){
                    $("#display_error_config").removeClass('cperror');
                    $("#display_error_config").addClass('cpsuccess');
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
 * 
 * @returns {string}
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
 *
 * @param data
 * @returns {boolean}
 */
function validateSendConfig(data){
    if(data.publickey != '' && data.privatekey != ''){
        if(data.webhook != ''){
            return true;
        }else{
            alert('El Webhook no debe de ser vacio.\nSi perdio la ruta por defecto recargue la pagina para obtenerlo' +
                ' de nuevo o ingrese la ruta manualmente.');
        }
    }else{
        alert('Las llaves no deben de estar vacias');
    }

    return false;
}

/**
 * Display retro info
 * 
 * @param {any} retro 
 */
function renderRetro(retro){
    if(retro[0]){
        $("#retro").html(retro[1]);
        $("#retro").css('display', 'block');
    }else{
        $("#retro").css('display', 'none');
    }
}
