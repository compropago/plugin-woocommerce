<div class="wrap compropago">
    <div class="nav-tab-wrapper" style="margin-bottom: 1em;">
        <a href="#" class="nav-tab nav-tab-active" onclick="openTab(event, 'config')">Configuración General</a>
        <a href="#" class="nav-tab" onclick="openTab(event, 'cash')">Efectivo</a>
        <a href="#" class="nav-tab" onclick="openTab(event, 'spei')">SPEI</a>
    </div>

    <div class="notice" id="display_error_config" style="padding:1em; display: none;">asdasd</div>

    <div id="config" class="tabcontent" style="display: block;">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row">Llave Publica</th>
                    <td class="forminp forminp-text">
                        <input type="text" name="publickey" id="publickey" placeholder="pk_live_xxxxxxxxxxxxxxx" value="<?php echo $publickey ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Llave Privada</th>
                    <td class="forminp forminp-text">
                        <input type="text" name="privatekey" id="privatekey" placeholder="sk_live_xxxxxxxxxxxxxxx" value="<?php echo $privatekey; ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Reducción de Inventario</th>
                    <td class="forminp forminp-text">
                        <select name="complete_order" id="complete_order">
                            <option value="init" <?php echo $complete_order == 'init' ? 'selected' : ''; ?>>Al crear la orden</option>
                            <option value="fin" <?php echo $complete_order == 'fin' ? 'selected' : ''; ?>>Al confirmar un pago</option>
                            <option value="no" <?php echo $complete_order == 'no' ? 'selected' : ''; ?>>Nunca</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Estatus inicial de la orden</th>
                    <td class="forminp forminp-text">
                        <select class="select2-selection__rendered" name="initial_state" id="intial_state">
                            <option value="pending" <?php echo $initial_state == 'pending' ? 'selected' : ''; ?>>Pendiente de pago</option>
                            <option value="on-hold" <?php echo $initial_state == 'on-hold' ? 'selected' : ''; ?>>En espera</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Debug</th>
                    <td class="forminp forminp-text">
                        <label class="switch">
                            <input type="checkbox" name="debug" id="debug" <?php echo ($debug === true) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="cash" class="tabcontent">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row">Activar Metodo Efectivo</th>
                    <td class="forminp forminp-text">
                        <label class="switch">
                            <input type="checkbox" name="enabled_cash" id="enable_cash" <?php echo ($cash_enable === true) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Titulo</th>
                    <td class="forminp forminp-text">
                        <input type="text" name="cash_title" id="cash_title" placeholder="Titulo" value="<?php echo $cash_title ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Proveedores</th>
                    <td class="forminp forminp-text">
                        <div class="wrap-providers">
                            Habilitados <br>
                            <select name="prov-allowed" id="prov-allowed" multiple>
                                <?php foreach ($active_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider->internal_name}'>{$provider->name}</option>"; ?>
                                <?php } ?>
                            </select><br>
                            <input type="button" value="Deshabilitar" id="quitar_proveedor" class="button-primary" style="width: 100%">
                        </div>
                        <div class="wrap-providers">
                            Deshabilitados <br>
                            <select name="prov-disabled" id="prov-disabled" multiple>
                                <?php foreach ($disabled_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider->internal_name}'>{$provider->name}</option>"; ?>
                                <?php } ?>
                            </select><br>
                            <input type="button" value="Habilitar" id="agregar_proveedor" class="button-primary" style="width: 100%">
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
                    <th class="titledesc" scope="row">Activar Metodo SPEI</th>
                    <td class="forminp forminp-text">
                        <label class="switch">
                            <input type="checkbox" name="enabled_spei" id="enable_spei" <?php echo ($spei_enable === true) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">Titulo</th>
                    <td class="forminp forminp-text">
                        <input type="text" name="spei_title" id="spei_title" placeholder="Titulo" value="<?php echo $spei_title ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>

    <input type="button" class="button-primary" value="Guardar configuración" id="save-config-compropago">
    <img id="loading" src="<?php echo $image_load; ?>" alt="Loading...">
    <input type="hidden" id="webhook" value="<?php echo $webhook; ?>">
    <input type="hidden" id="url-save" value="<?php echo $configUrl; ?>">
</div>

<div class="compropago cp-modal active">
    <div class="cp-modal-dialog">
        <div class="cp-modal-body">
            <div class="xl-modal-panel" data-panel-id="confirm"><p></p></div>
            <div class="xl-modal-panel active" data-panel-id="reasons">
                <h3><strong>Modo activo o modo pruebas:</strong></h3>
                <p>Indica a que modo corresponden las llaves que actualmente estas configurando.</p>

                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row">Modo Activo</th>
                        <?php var_dump($live); ?>
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
            <input type="button" class="button-primary" value="Guardar" id="save-all">
        </div>
    </div>
</div>