<?php

namespace CompropagoSdk\Factory\Models;

class Instructions
{
	public $description;
    public $step_1;
    public $step_2;
    public $step_3;
    public $note_extra_comition;
    public $note_expiration_date;
    public $note_confirmation;
    public $details;

    public function __construct()
    {
    	$this->details = new InstructionDetails();
    }
}