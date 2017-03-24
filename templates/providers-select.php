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
              <?php if($comprodata['showlogo'] == 'yes') { ?>

                  <ul>
                      <?php foreach ($comprodata['providers'] as $provider){ ?>
                          <li>
                              <input type="radio" id="compropago_<?php echo $provider->internal_name; ?>" name="compropagoProvider" value="<?php echo $provider->internal_name; ?>">
                              <label for="compropago_<?php echo $provider->internal_name; ?>">
                                  <img src="<?php echo $provider->image_medium; ?>" alt="compropago_<?php echo $provider->internal_name; ?>">
                              </label>
                          </li>
                      <?php } ?>
                  </ul>

              <?php } else { ?>

                  <select name="compropagoProvider" title="Proveedores">
                      <?php foreach ($comprodata['providers'] as $provider){ ?>
                          <option value="<?php echo $provider->internal_name; ?>"> <?php echo $provider->name; ?> </option>
                      <?php } ?>
                  </select>

              <?php } ?>

              <?php } else {echo( __('Éste método de pago temporalmente está fuera de servicio.', 'compropago')); }?>


        </div>
    </div>
</section>


<script>
    var providers = document.querySelectorAll(
            ".cpcontainer.cpprovider-select ul li label img"
    );
    for (x = 0; x < providers.length; x++){
        providers[x].addEventListener('click', function(){
            cleanCpRadio();
            id = this.getAttribute("alt");
            document.querySelector("#"+id).checked = true;
        });
    }
    function cleanCpRadio(){
        for(y = 0; y < providers.length; y++){
            id = providers[y].parentNode.getAttribute('for');
            document.querySelector("#"+id).checked = false;
        }
    }
</script>
