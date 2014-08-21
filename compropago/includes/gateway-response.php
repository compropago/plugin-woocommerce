<?php

/**
* Gateway API response class
**/
class compropago_response {
		
	public $results;

	/** constructor */
	public function __construct( $results ) {
		$this->results = $results;
	}

	/**
	 * Return whether or not the request was successful
	 * Check paid == true?
	**/
	public function success($action='') {
		if(!empty($this->results) 
			&& is_object($this->results)
			&& (!empty($this->results->id) || ($action=='cancel' && !empty($this->results->status)  && $this->results->status=='canceled'))){
			return true;
		}
		
		return false;
	}
	
	/**
	* Get declined message
	**/
	public function get_error($attr='') {
		if(empty($this->results))
			return __('response fail');
		
		if(!$this->success()) {
			if($this->results instanceof Exception){
				return $this->results->getMessage();
			}
			return $this->results;
		}
	}
	
	/**
	 * Live mode status 
	 */
	public function get_testmode(){

		if(empty($this->results->livemode)) return 'yes';
		
		if($this->results->livemode==false || $this->results->livemode=='false'){
			return 'yes';
		}
		return 'no';
	}
	
	/**
	* Get transaction id
	**/
	public function get_transaction_id() {
		if (isset($this->results->id)) 
			return $this->results->id;
	}
	
}
?>