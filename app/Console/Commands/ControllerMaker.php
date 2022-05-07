<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ControllerMaker extends  GeneratorCommand
{

    protected $name = 'make:controller';

    protected $description = 'create a new controller';

    protected $type = 'Controller';

	/**
     * TODO: Implement getStub() method.
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__.'/template/controller.stub';
	}

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
	}
}
