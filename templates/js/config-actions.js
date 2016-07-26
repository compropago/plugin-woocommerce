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
            publickey: $('#publickey').val(),
            privatekey: $('#privatekey').val(),
            live: $('#live').is(':checked') ? 'yes' : 'no',
            showlogo: $('#showlogo').is(':checked') ? 'yes' : 'no',
            webhook: $('#webhook').val(),
            provallowed: getProvidersAllowed(),
            descripcion: $('#descripsion').val(),
            instrucciones: $('#instrucciones').val()
        };

        if(validateSendConfig(data)){
            $.ajax({
                //url: ':url-controller:',
                url: 'http://demo.compropago.com/pruebas/wc/wp-content/plugins/newcompropago/controllers/ConfigController.php',
                type: 'post',
                data: data,
                success: function(res){
                    if(res.error){
                        $("#display_error_config").removeClass('cpsuccess');
                        $("#display_error_config").addClass('cperror');
                    }else{
                        $("#display_error_config").removeClass('cperror');
                        $("#display_error_config").addClass('cpsuccess');
                    }


                    $("#display_error_config").html(res.message);
                    $("#display_error_config").fadeIn();
                    $("#display_error_config").removeClass('error');

                    console.log(res);
                },
                error: function(res){
                    $("#display_error_config").removeClass('cperror');
                    $("#display_error_config").addClass('cpsuccess');
                    $("#display_error_config").html(res);
                    $("#display_error_config").fadeIn();
                    $("#display_error_config").removeClass('error');

                    console.log(res);
                }
            });

            $("#loadig").fadeOut();
        }
    });

});


function getProvidersAllowed(){
    active = '';

    $('#prov-allowed option').each(function(){
        if(active == ''){
            active += $(this).val();
        }else{
            active += ','+$(this).val();
        }
    });

    console.log(active);

    return active;
}

function validateSendConfig(data){
    if(data.publickey != '' && data.privatekey != ''){
        if(data.webhook != ''){
            if(data.provallowed != ''){
                return true;
            }else{
                alert('Debe activar al menos un proveedor');
                return false;
            }
        }else{
            alert('El Webhook no debe de ser vacio.\nSi perdio la ruta por defecto recargue la pagina para obtenerlo de nuevo' +
                ' o ingrese la ruta manualmente.');
            return false;
        }
    }else{
        alert('Las llaves no deben de estar vacias');
        return false;
    }
}