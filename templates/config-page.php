<section class="cpcontainer">
    <div class="row" id="top">
        <div class="large-12 columns">
            <h1>ComproPago Config</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <div class="cpalert" style="display:<?php echo $retro[0] ? 'block' : 'none'; ?>" id="retro">
                <?php echo $retro[1]; ?>
            </div>
            <br>
            <div class="cpalert cperror" id="display_error_config" style="display: none"></div>
            <br>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <div class="tab">
                <button class="tablinks active" onclick="openTab(event, 'config')">Configuración General</button>
                <button class="tablinks" onclick="openTab(event, 'cash')">Efectivo</button>
                <button class="tablinks" onclick="openTab(event, 'spei')">SPEI</button>
            </div>

            <div id="config" class="tabcontent" style="display: block;">
                <div class="row">
                    <div class="large-12 columns">
                        <div class="text-center" style="width: 50%; float: left;">
                            <label>Modo Activo</label>
                            <label class="switch">
                              <input type="checkbox" name="live" id="live" <?php echo ($live === true) ? 'checked' : ''; ?>>
                              <span class="slider"></span>
                            </label>
                        </div>

                        <div class="text-center" style="width: 50%; float: left;">
                            <label>Debug</label>
                            <label class="switch">
                              <input type="checkbox" name="debug" id="debug" <?php echo ($debug === true) ? 'checked' : ''; ?>>
                              <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <br><br>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="publickey">
                            Llave Publica
                            <input type="text" name="publickey" id="publickey" placeholder="pk_live_xxxxxxxxxxxxxxx" value="<?php echo $publickey ?>">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="privatekey">
                            Llave Privada
                            <input type="text" name="privatekey" id="privatekey" placeholder="sk_live_xxxxxxxxxxxxxxx" value="<?php echo $privatekey; ?>">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="complete_order">
                            Reduccion de Inventario
                            <select name="complete_order" id="complete_order">
                                <option value="init" <?php echo $complete_order == 'init' ? 'selected' : ''; ?>>Al crear la orden</option>
                                <option value="fin" <?php echo $complete_order == 'fin' ? 'selected' : ''; ?>>Al confirmar un pago</option>
                                <option value="no" <?php echo $complete_order == 'no' ? 'selected' : ''; ?>>Nunca</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="intial_state">
                            Estatus inicial de la orden
                            <select name="initial_state" id="intial_state">
                                <option value="pending" <?php echo $initial_state == 'pending' ? 'selected' : ''; ?>>Pendiente de pago</option>
                                <option value="on-hold" <?php echo $initial_state == 'on-hold' ? 'selected' : ''; ?>>En espera</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>

            <div id="cash" class="tabcontent">
                <div class="row">
                    <div class="large-12 columns text-center">
                        <label>Activar Efectivo</label>
                        <label class="switch">
                          <input type="checkbox" name="enabled_cash" id="enable_cash" <?php echo ($cash_enable === true) ? 'checked' : ''; ?>>
                          <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <br><br>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="cash_title">
                            Titulo
                            <input type="text" name="cash_title" id="cash_title" placeholder="Titulo" value="<?php echo $cash_title ?>">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="columns">
                        <label for="prov-disabled">
                            Proveedores Deshabilitados
                            <select name="prov-disabled" id="prov-disabled" multiple>
                                <?php foreach ($disabled_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider->internal_name}'>{$provider->name}</option>"; ?>
                                <?php } ?>
                            </select>
                        </label>
                        <input type="button" value="Habilitar" id="agregar_proveedor" class="button primary expanded">
                    </div>

                    <div class="columns">
                        <label for="prov-allowed">
                            Proveedores Habilitados
                            <select name="prov-allowed" id="prov-allowed" multiple>
                                <?php foreach ($active_providers as $provider) { ?>
                                    <?php echo "<option value='{$provider->internal_name}'>{$provider->name}</option>"; ?>
                                <?php } ?>
                            </select>
                        </label>
                        <input type="button" value="Deshabilitar" id="quitar_proveedor" class="button primary expanded">
                    </div>
                </div>
            </div>

            <div id="spei" class="tabcontent">
                <div class="row">
                    <div class="large-12 columns text-center">
                        <label>Activar SPEI</label>
                        <label class="switch">
                          <input type="checkbox" name="enabled_spei" id="enable_spei" <?php echo ($spei_enable === true) ? 'checked' : ''; ?>>
                          <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <br><br>

                <div class="row">
                    <div class="large-12 columns">
                        <label for="spei_title">
                            Titulo
                            <input type="text" name="spei_title" id="spei_title" placeholder="Titulo" value="<?php echo $spei_title ?>">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns"><br></div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <input type="button" class="button primary expanded" value="Guardar configuracion" id="save-config-compropago">
        </div>
    </div>

    <input type="hidden" name="webhook" id="webhook" value="<?php echo $webhook; ?>">
</section>

<section class="cp-block-login" id="loadig">
    <img src="<?php echo $image_load; ?>" alt="Loading...">
</section>