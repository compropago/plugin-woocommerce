<section class="compropago">
    <p>
        <b><?php echo __('¿Dónde quieres pagar?*'); ?></b>
    </p>

    <?php if($comprodata["providers"] != 0){ ?>

        <select name="compropagoProvider" title="Corresponsales" style="width: 100%">
            <?php foreach ($comprodata['providers'] as $provider) { ?>
                <option value="<?php echo $provider['internal_name']; ?>">
                    <?php echo $provider['name']; ?>
                </option>
            <?php } ?>
        </select>

        <p class="active">
            <a href="https://compropago.com/legal/corresponsales_cnbv.pdf"
               target="_blank">
                <small>
                    <?php echo __('* Comisionistas <b>autorizados por la CNBV</b> como corresponsales bancarios'); ?>
                </small>
            </a>
        </p>
    <?php } else {
        echo( __('No hay proveedores disponibles para realizar el pago.', 'compropago'));
    } ?>
</section>
