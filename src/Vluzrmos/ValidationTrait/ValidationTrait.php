<?php namespace Vluzrmos\ValidationTrait;

use Illuminate\Support\Facades\App;

trait ValidationTrait{
	protected $errors   = null;

	public static function bootValidationTrait(){
		static::saving(function($model){ return $model->passes(); });
	}

	public function validate(){
		$validator = App::make("validator");

		$this->errors = $validator->make(
      $this->getAttributes(), 
      $this->getValidationRules(), 
      $this->getValidationMessages(), 
      $this->getValidationCustomAttributes()
    );

		return $this->errors;
	}

	public function passes(){
		return $this->validate()->passes();
	}

	public function fails(){
		return $this->validate()->fails();
	}

	public function getValidationRules(){
		$rules = isset($this->rules) ? $this->rules : [];

		//Configurando a regra unique
		foreach($rules as $i => &$rule){
			$rule = str_replace("unique", "unique:".$this->getTable().",{$i}".(isset($this->id)? ",".$this->id:""), $rule);
		}

		return $rules;
	}

	public function getValidationMessages(){
		return isset($this->messages) ? $this->messages : [];
	}

	public function getValidationCustomAttributes(){
		return isset($this->customAttributes) ? $this->customAttributes : [];
	}

	public function getErrors(){
		return $this->errors;
	}
}