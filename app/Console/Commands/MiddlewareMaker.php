<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MiddlewareMaker extends GeneratorCommand
{
    protected $name = 'make:middleware';

    protected $description = 'create a new middleware';

    protected $type = 'Middleware';

	/**
     * TODO: Implement getStub() method.
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__.'/template/middleware.stub';
	}

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Middleware';
	}
}
