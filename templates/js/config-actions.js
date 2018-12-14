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

$(function() {
	$("#agregar_proveedor").click(function() {
		$("#prov-disabled option").each(function() {
			if($(this).is(':selected')){
				$('#prov-allowed').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
				$(this).remove();
			}
		});
	});

	$('#quitar_proveedor').click(function() {
		$('#prov-allowed option').each(function() {
			if($(this).is(':selected')){
				$('#prov-disabled').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
				$(this).remove();
			}
		});
	});

	$("#save-config-compropago").click(function() {
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
	let
		loadImage = $('#loading'),
		tr_publickey = $('#publickey').parent().parent(),
		tr_privatekey = $('#privatekey').parent().parent();

	const data = {
		cash_enable:	$('#enable_cash').is(':checked') ? 'yes' : 'no',
		spei_enable:	$('#enable_spei').is(':checked') ? 'yes' : 'no',
		cash_title:     $('#cash_title').val(),
		spei_title:     $('#spei_title').val(),
		publickey:      $('#publickey').val(),
		privatekey:     $('#privatekey').val(),
		webhook:        $('#webhook').val(),
		provallowed:    getProvidersAllowed(),
		complete_order: $('#complete_order').val(),
		initial_state:  $('#intial_state').val(),
		debug:          'no',
	};

	loadImage.fadeIn();

	if (live !== 'no') data.live = live;

	if (validateSendConfig(data))
	{
		tr_publickey.removeClass('form-invalid');
		tr_privatekey.removeClass('form-invalid');

		$.ajax({
			url: $('#url-save').val(),
			type: 'post',
			data: data,
			success: function(json)
			{
				showMessage(
					json.message,
					(json.error ? 'error' : 'success')
				);
			},
			error: function(res)
			{
				let json = res.responseJSON;
				let message = ( json && json.hasOwnProperty('message') )
					? json.message
					: 'Error al guardar configuración';
				
				showMessage(message, 'error');
			}
		});
	}
	else {
		tr_publickey.addClass('form-invalid');
		tr_privatekey.addClass('form-invalid');
	}

	loadImage.fadeOut();
	modal.fadeOut();
}

/**
 * Get allow providers
 * @return {string}
 */
function getProvidersAllowed() {
	var active = [];

	$('#prov-allowed option').each(function()
	{
		active.push( $(this).val() );
	});

	if (active.length === 0)
	{
		$('#prov-disabled option').each(function() {
			active.push( $(this).val() );
		});
	}

	return active.join(',');
}

/**
 * Validate Compropago Keys not empty
 * @param {Object} data
 * @return {boolean}
 */
function validateSendConfig(data)
{
	if (data.publickey.length === 0 || data.privatekey.length === 0)
	{
		showMessage("Las llaves no deben de estar vacías", 'error');
		return false;
	}

	return true;
}

/**
 * Open a tab menu
 * @param {Object} evt 
 * @param {string} tabName 
 */
function openTab(evt, tabName)
{
	var i, tabcontent, tablinks;

	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++)
	{
		tabcontent[i].style.display = "none";
	}

	tablinks = document.getElementsByClassName("nav-tab");
	for (i = 0; i < tablinks.length; i++)
	{
		tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
	}

	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className += " nav-tab-active";
}

/**
 * Show notice message
 * @param {string} message 
 * @param {string} type 
 */
function showMessage(message, type) {
	let
		error_obj = $('#display_error_config'),
		error_class = error_obj.attr("class").split(' ');
	
	for (let I in error_class) {
		if (error_class[I].startsWith("notice-") ) {
			error_obj.removeClass(error_class[I]);
		}
	}
	error_obj.addClass(`notice-${type}`);
	error_obj.text(message);
	error_obj.fadeIn();

	$(document).scrollTop($("body").offset().top);

	window.setTimeout(function() {
		error_obj.fadeOut();
	}, 10000);
}
