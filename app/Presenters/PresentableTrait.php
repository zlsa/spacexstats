<?php
namespace SpaceXStats\Presenters;

trait PresentableTrait {
	protected static $presenterInstance;

	public function present() {

		if (!$this->presenter || !class_exists($this->presenter)) {
			throw new \Exception('Please set the protected $presenter property, or if it is set, run \'composer dump-autoload\'.');
		}

		if (!isset(static::$presenterInstance)) {
			static::$presenterInstance = new $this->presenter($this);	
		}

		return static::$presenterInstance; 
	}
}