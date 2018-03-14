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
        <div class="small-12 columns">
            <div class="text-center" style="width: 33%; float: left;">
                <p>Activar método de pago</p>
                <div class="switch small">
                    <input class="switch-input" type="checkbox" name="enabled" id="enabled" style="margin-top: 10px" <?php echo ($enabled === true) ? 'checked' : ''; ?>>
                    <label class="switch-paddle" for="enabled">
                        <span class="switch-active" aria-hidden="true">Si</span>
                        <span class="switch-inactive" aria-hidden="true">No</span>
                    </label>
                </div>
            </div>
            <div class="text-center" style="width: 33%; float: left;">
                <p>Modo Activo</p>
                <div class="switch small">
                    <input class="switch-input" type="checkbox" name="live" id="live" style="margin-top: 10px" <?php echo ($live === true) ? 'checked' : ''; ?>>
                    <label class="switch-paddle" for="live">
                        <span class="switch-active" aria-hidden="true">Si</span>
                        <span class="switch-inactive" aria-hidden="true">No</span>
                    </label>
                </div>
            </div>
            <div class="text-center" style="width: 33%; float: left;">
                <p>Debug</p>
                <div class="switch small">
                    <input class="switch-input" type="checkbox" name="debug" id="debug" style="margin-top: 10px" <?php echo ($debug === 'yes') ? 'checked' : ''; ?>>
                    <label class="switch-paddle" for="debug">
                        <span class="switch-active" aria-hidden="true">Si</span>
                        <span class="switch-inactive" aria-hidden="true">No</span>
                    </label>
                </div>
            </div>
        </div>

        <!--
        <div class="small-12 medium-3 large-3 columns">
          <p>Mostrar Logos</p>
          <div class="switch small">
            <input class="switch-input" type="checkbox" name="showlogo" id="showlogo" style="margin-top: 10px" <?php #echo ($showlogo === true) ? 'checked' : ''; ?>>
            <label class="switch-paddle" for="showlogo">
              <span class="switch-active" aria-hidden="true">Si</span>
              <span class="switch-inactive" aria-hidden="true">No</span>
            </label>
          </div>
        </div>
        -->
    </div>

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
            <label for="webhook">
                <input type="hidden" name="webhook" id="webhook" value="<?php echo $webhook; ?>">
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

    <div class="row">
        <div class="large-12 columns">
            <label for="title">
                Titulo
                <input type="text" name="title" id="title" value="<?php echo $titulo ? $titulo : 'ComproPago (OXXO, 7Eleven, etc.)'; ?>">
            </label>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <label for="descripsion">
                Descripcion
                <input type="text" name="descripcion" id="descripsion" value="<?php echo $descripcion ? $descripcion : 'Con ComproPago, realiza tus pagos en Oxxo, 7Eleven y mas...' ?>">
            </label>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <label for="select-text">
                Instrucciones
                <input type="text" name="select-text" id="instrucciones" value="<?php echo $instrucciones ? $instrucciones : 'Selecciona el establecimiento donde deseas pagar'; ?>">
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
                    <option value="on-hold" <?php echo $initial_state == 'on-hold' ? 'selected' : ''; ?>>On Hold</option>
                    <option value="pending" <?php echo $initial_state == 'pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </label>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <input type="button" class="button primary expanded" value="Guardar configuracion" id="save-config-compropago">
        </div>
    </div>
</section>

<section class="cp-block-login" id="loadig">
    <img src="<?php echo $image_load; ?>" alt="Loading...">
</section>