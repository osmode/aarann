<?php

class Entity {

	public $level;
	public $id;
	
	function __construct($level_in,$id_in) {
		$this->$level=$level_in;
		$this->$id=$id_in;
	}

?>
	