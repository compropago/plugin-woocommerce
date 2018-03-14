<section class="cpcontainer cpprovider-select">
    <div class="row">
        <div class="column">
            <h3><?php echo $comprodata['description']; ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="column">
            <?php echo $comprodata['instrucciones']; ?> <br>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="column">
            <?php if($comprodata["providers"] != 0){ ?>

                <select name="compropagoProvider" title="Proveedores">
                    <?php foreach ($comprodata['providers'] as $provider){ ?>
                        <option value="<?php echo $provider->internal_name; ?>"> <?php echo $provider->name; ?> </option>
                    <?php } ?>
                </select>

                <p class="active">
                    <a href="https://s3.amazonaws.com/compropago/documents/cnvb/corresponsales_bancarios.pdf"
                       target="_blank">
                        <small>
                            Establecimientos <b>autorizados por la CNBV</b> como corresponsales bancarios
                        </small>
                    </a>
                </p>

                <input type="hidden" id="cp_longitude" name="cp_longitude">
                <input type="hidden" id="cp_latitude" name="cp_latitude">
            <?php } else { echo( __('Temporalmente fuera de servicio.', 'compropago')); }?>
        </div>
    </div>
</section>
