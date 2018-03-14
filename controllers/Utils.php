<?php
/**
 * Compropago $Library
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */

use CompropagoSdk\Client;
use CompropagoSdk\Tools\Validations;

class Utils
{

    public static function retroalimentacion($publickey, $privatekey, $live, $config)
    {
        $error = array(
            false,
            '',
            'yes'
        );

        if($config['enabled']=='yes'){
            if(!empty($publickey) && !empty($privatekey) ){

                try{
                    $client = new Client(
                        $publickey,
                        $privatekey,
                        $live
                    );

                    $compropagoResponse = Validations::evalAuth($client);

                    //eval keys
                    if(!Validations::validateGateway($client)){
                        $error[1] = 'Invalid Keys, The Public Key and Private Key must be valid before using this module.';
                        $error[0] = true;
                    }else{
                        if($compropagoResponse->mode_key != $compropagoResponse->livemode){
                            // compropagoKey vs compropago Mode
                            $error[1] = 'Your Keys and Your ComproPago account are set to different Modes.';
                            $error[0] = true;
                        }else{
                            if($live != $compropagoResponse->livemode){
                                // store Mode vs compropago Mode
                                $error[1] = 'Your Store and Your ComproPago account are set to different Modes.';
                                $error[0] = true;
                            }else{
                                if($live != $compropagoResponse->mode_key){
                                    // store Mode vs compropago Keys
                                    $error[1] = 'ComproPago ALERT:Your Keys are for a different Mode.';
                                    $error[0] = true;
                                }else{
                                    if(!$compropagoResponse->mode_key && !$compropagoResponse->livemode){
                                        //can process orders but watch out, NOT live operations just testing
                                        $error[1] = 'WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS';
                                        $error[0] = true;
                                    }
                                }
                            }
                        }
                    }
                }catch (Exception $e) {
                    //something went wrong on the SDK side
                    $error[2] = 'no';
                    $error[1] = $e->getMessage(); //may not be show or translated
                    $error[0] = true;
                }
            }else{
                $error[1] = 'The Public Key and Private Key must be set before using ComproPago';
                $error[2] = 'no';
                $error[0] = true;
            }
        }else{
            $error[1] = 'ComproPago is not Enabled';
            $error[2] = 'no';
            $error[0] = true;
        }

        return $error;
    }

}