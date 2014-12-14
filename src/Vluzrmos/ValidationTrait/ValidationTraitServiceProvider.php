<?php namespace Vluzrmos\ValidationTrait;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class ValidationTraitServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package("vluzrmos/validationtrait");

		AliasLoader::getInstance()->alias('ValidationTrait', 'Vluzrmos\ValidationTrait\ValidationTrait');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
