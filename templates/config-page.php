<div class="wrap compropago">

    <h1><?php echo __("ComproPago"); ?></h1>
    
    <div class="nav-tab-wrapper" style="margin-bottom: 1em;">
        <a href="#" class="nav-tab nav-tab-active" onclick="openTab(event, 'config')">Configuración general</a>
        <a href="#" class="nav-tab" onclick="openTab(event, 'cash')">Efectivo</a>
        <a href="#" class="nav-tab" onclick="openTab(event, 'spei')">SPEI</a>
    </div>

    <div class="notice" id="display_error_config" style="padding: 1em; display: none;">Error config message</div>

    <div id="config" class="tabcontent" style="display: block;">
        
        <!-- Llaves del API -->
        <h2>
            <span class="dashicons dashicons-post-status"></span> <?php echo __("Llaves del API"); ?>
        </h2>
        <p class="description">
            Puedes consultar esta información en el
            <a href="https://panel.compropago.com/panel/configuracion" target="_BLANK">Panel de ComproPago</a>.
        </p>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Llave pública"); ?></th>
                    <td class="forminp forminp-text">
                        <input type="text" name="publickey" id="publickey" placeholder="pk_live_xxxxxxxxxxxxxxx" value="<?php echo $publickey ?>" required>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Llave privada"); ?></th>
                    <td class="forminp forminp-text">
                        <input type="text" name="privatekey" id="privatekey" placeholder="sk_live_xxxxxxxxxxxxxxx" value="<?php echo $privatekey; ?>" required>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2>
            <span class="dashicons dashicons-cart"></span> <?php echo __("Ordenes"); ?>
        </h2>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Reducción de inventario"); ?></th>
                    <td class="forminp forminp-text">
                        <select name="complete_order" id="complete_order">
                            <option value="init" <?php echo $complete_order == 'init' ? 'selected' : ''; ?>><?php echo __("Al crear la orden"); ?></option>
                            <option value="fin" <?php echo $complete_order == 'fin' ? 'selected' : ''; ?>><?php echo __("Al confirmar un pago"); ?></option>
                            <option value="no" <?php echo $complete_order == 'no' ? 'selected' : ''; ?>><?php echo __("Nunca"); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Estatus inicial de la orden"); ?></th>
                    <td class="forminp forminp-text">
                        <select class="select2-selection__rendered" name="initial_state" id="intial_state">
                            <option value="pending" <?php echo $initial_state == 'pending' ? 'selected' : ''; ?>><?php echo __("Pendiente de pago"); ?></option>
                            <option value="on-hold" <?php echo $initial_state == 'on-hold' ? 'selected' : ''; ?>><?php echo __("En espera"); ?></option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2>
            <span class="dashicons dashicons-admin-links"></span> <?php echo __("Webhook"); ?>
        </h2>
        <p class="description">
            <?php echo __("Recibir notificación cuando un pago se haya efectuado. Puede consultar los Webhook activos"); ?>
            <a href="https://panel.compropago.com/panel/webhooks_list" target="_BLANK">aquí</a>.
        </p>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("URL"); ?></th>
                    <td class="forminp forminp-text">
                        <textarea readonly rows="2" style="resize: none; background-color: white;"><?php echo $webhook; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="cash" class="tabcontent">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Activar Método Efectivo"); ?></th>
                    <td class="forminp forminp-text">
                        <label class="switch">
                            <input type="checkbox" name="enabled_cash" id="enable_cash" <?php echo ($cash_enable === true) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Título"); ?></th>
                    <td class="forminp forminp-text">
                        <input type="text" name="cash_title" id="cash_title" placeholder="<?php echo __("Título"); ?>" value="<?php echo $cash_title ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Proveedores"); ?></th>
                    <td class="forminp forminp-text">
                        <div class="wrap-providers">
                            Habilitados
                            <br/>
                            <select name="prov-allowed" id="prov-allowed" multiple>
                                <?php foreach ($active_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider['internal_name']}'>{$provider['name']}</option>"; ?>
                                <?php } ?>
                            </select>
                            <br/>
                            <input type="button" value="<?php echo __("Deshabilitar"); ?>" id="quitar_proveedor" class="button-primary" style="width: 100%;">
                        </div>
                        <div class="wrap-providers">
                            <?php echo __("Deshabilitados") ?>
                            <br />
                            <select name="prov-disabled" id="prov-disabled" multiple>
                                <?php foreach ($disabled_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider['internal_name']}'>{$provider['name']}</option>"; ?>
                                <?php } ?>
                            </select><br>
                            <input type="button" value="Habilitar" id="agregar_proveedor" class="button-primary" style="width: 100%;">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="spei" class="tabcontent">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Activar Método SPEI"); ?></th>
                    <td class="forminp forminp-text">
                        <label class="switch">
                            <input type="checkbox" name="enabled_spei" id="enable_spei" <?php echo ($spei_enable === true) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><?php echo __("Título"); ?></th>
                    <td class="forminp forminp-text">
                        <input type="text" name="spei_title" id="spei_title" placeholder="<?php echo __("Título"); ?>" value="<?php echo $spei_title ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>

    <input type="button" class="button-primary" value="<?php echo __("Guardar configuración"); ?>" id="save-config-compropago">
    <img id="loading" src="<?php echo $image_load; ?>" alt="<?php echo __("Cargando..."); ?>">
    
    <input type="hidden" id="webhook" value="<?php echo $webhook; ?>">
    <input type="hidden" id="url-save" value="<?php echo $configUrl; ?>">
</div>

<div class="compropago cp-modal active">
    <div class="cp-modal-dialog">
        
        <div class="cp-modal-body">
            <div class="xl-modal-panel" data-panel-id="confirm"><p></p></div>
            <div class="xl-modal-panel active" data-panel-id="reasons">
                <h3><strong><?php echo __("Modo activo o modo pruebas:"); ?></strong></h3>
                <p><?php echo __("Indica a que modo corresponden las llaves que actualmente estas configurando."); ?></p>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row"><?php echo __("Modo Activo"); ?></th>
                        <td class="forminp forminp-text">
                            <label class="switch">
                                <input type="checkbox" id="live" <?php echo ($live === true) ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cp-modal-footer">
            <input type="button" class="button-primary" value="<?php echo __("Guardar"); ?>" id="save-all">
        </div>
    </div>
</div>
