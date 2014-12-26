<?php namespace Vluzrmos\ValidationTrait;

use Illuminate\Support\Facades\App;

/**
 * Trait para validação de models
 * 
 * @property boolean $autoIdentifyUniqueRules Se ativado, as regras uniques devem ser resumidas e serão 
 * preenchidas automaticamente pela trait
 * 
 * @property  boolean $autoIgnoreDeletedAtColumn Se ativado, vai adicionar na regra unique 
 * a opção para ignorar registros apagados
 */
trait ValidationTrait{
	/**
	 * MessageBag|Validator com os erros gerados
	 * @var MessageBag|Validator
	 */
	private $errors   = null;

	private static $useSoftDeletingTrait = false;

	/**
	 * Inicializa a trait e configura para observar o evento saving do Eloquent
	 * @return [type] [description]
	 */
	public static function bootValidationTrait(){
		static::saving(function($model){ return $model->passes(); });

		static::$useSoftDeletingTrait = in_array('Illuminate\Database\Eloquent\SoftDeletingTrait', class_uses(get_called_class()));
	}

	/**
	 * Gera a validacao 
	 * @return Validator
	 */
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
		
		if(!isset($this->autoIdentifyUniqueRules) or $this->autoIdentifyUniqueRules == true){
			$rules = $this->identifyUniqueRules($rules);
		}

		return $rules;
	}

	protected function identifyUniqueRules($rulefields = []){
			$table = $this->getTable();

			//Configurando a regra unique
			foreach($rulefields as $field => &$ruleset){
				$ruleset = explode("|", $ruleset);

				foreach($ruleset as &$rule){
					if(starts_with($rule, "unique:")){
						$rule = $this->prepareUniqueRule($table, $field, substr($rule, 7));
					}
					elseif(starts_with($rule, "unique")){
						$rule = $this->prepareUniqueRule($table, $field, substr($rule, 6));
					}
				}

				$ruleset = implode("|", $ruleset);
			}	

			return $rulefields;
	}

	protected function prepareUniqueRule($table, $field, $uniqueparams){
		$uniqueparams = explode(",", $uniqueparams);

		if($uniqueparams[0] == "") unset($uniqueparams[0]);

		$defTable = array_get($uniqueparams, 0);

		if(empty($defTable) or $defTable == $table){
			$uniqueparams[0] = array_get($uniqueparams, 0, $table);
			$uniqueparams[1] = array_get($uniqueparams, 1, $field);
			$uniqueparams[2] = array_get($uniqueparams, 2, $this->id?:"NULL");
			$uniqueparams[3] = array_get($uniqueparams, 3, "id" );

			if(!isset($this->autoIgnoreDeletedAtColumn) or $this->autoIgnoreDeletedAtColumn == true and self::$useSoftDeletingTrait){
					$uniqueparams[4] = array_get($uniqueparams, 4, $this->getDeletedAtColumn());
					$uniqueparams[5] = array_get($uniqueparams, 5, "NULL");
			}
		}
		else{
			$uniqueparams[1] = array_get($uniqueparams, 1, $field);
			$uniqueparams[2] = array_get($uniqueparams, 2, "NULL" );
			$uniqueparams[3] = array_get($uniqueparams, 3, "id" );
		}

		return "unique:".implode(",", $uniqueparams);
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