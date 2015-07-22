Laravel ValidationTrait
=======================

### That package is no longer maintained, consider to use [watson/validating](https://github.com/dwightwatson/validating).

[![Latest Stable Version](https://poser.pugx.org/vluzrmos/validation-trait/v/stable.svg)](https://packagist.org/packages/vluzrmos/validation-trait) [![Total Downloads](https://poser.pugx.org/vluzrmos/validation-trait/downloads.svg)](https://packagist.org/packages/vluzrmos/validation-trait) [![Latest Unstable Version](https://poser.pugx.org/vluzrmos/validation-trait/v/unstable.svg)](https://packagist.org/packages/vluzrmos/validation-trait) [![License](https://poser.pugx.org/vluzrmos/validation-trait/license.svg)](https://packagist.org/packages/vluzrmos/validation-trait)

Simples facilitador para validação de models no Laravel 4.2.

Se procura um package mais completo, considere utilizar o [watson/validating](https://github.com/dwightwatson/validating)

Instalação
=

Adicione ao composer
```
composer require vluzrmos/validation-trait 1.0
```

Adicione à lista de services providers em app/app.php

```php
'providers' => array(
  ...
  ...

  'Vluzrmos\ValidationTrait\ValidationTraitServiceProvider',
)
```

Exemplos
=
```php
class User extends Eloquent{
  use ValidationTrait;
  
  $rules = [
    "username" => "required|unique", //Somente "unique" 
    "password" => "required|min:6"
  ];
  
  $messages = [
    "username.required" => "Nome de usuário obrigatório",
    "username.unique" => "Já existe usuário cadastrado com esse nome de usuário"
  ];
  
}
```
Agora, quando for criar/salvar uma instancia do model que não atenda aos requisitos em  $rules, o save() retornará false:
```php
$user = new User();

$user->username = Input::get("username");
$user->password = Input::get("password");

if($user->save()){
  Session::flash("success", "Salvou com sucesso");
  return Redirect::to("home");
}
else{
  return Redirect::back()->withInput()->withErrors($user->getErrors());
}
```

O ValidationTrait adiciona automaticamente os campos id e deleted_at às validações "unique",
então, basta fazer "field" => "required|unique", que automaticamente, no momento da validação, será modificado para "unique:table_name,field,{id|null},id,{deleted_at_column},NULL".

Mais opções de validação em [Laravel Validation Rules](http://laravel.com/docs/4.2/validation#available-validation-rules).
