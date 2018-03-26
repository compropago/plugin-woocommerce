<section class="cpcontainer cpprovider-select">
    <p>
        <b>¿Dónde quieres pagar?*</b>
    </p>

    <?php if($comprodata["providers"] != 0){ ?>

        <select name="compropagoProvider" title="Corresponsales">
            <?php foreach ($comprodata['providers'] as $provider){ ?>
                <option value="<?php echo $provider->internal_name; ?>"> <?php echo $provider->name; ?> </option>
            <?php } ?>
        </select>

        <p class="active">
            <a href="https://s3.amazonaws.com/compropago/documents/cnvb/corresponsales_bancarios.pdf"
               target="_blank">
                <small>
                    * Comisionistas <b>autorizados por la CNBV</b> como corresponsales bancarios
                </small>
            </a>
        </p>
    <?php } else { echo( __('Temporalmente fuera de servicio.', 'compropago')); }?>
</section>
