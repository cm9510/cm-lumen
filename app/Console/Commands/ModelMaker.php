<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ModelMaker extends GeneratorCommand
{
    protected $name = 'make:model';

    protected $description = 'create a new eloquent model';

    protected $type = 'Model';

	/**
     * TODO: Implement getStub() method.
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__.'/template/model.stub';
	}

    protected function getDefaultNamespace($rootNamespaces)
    {
        return $rootNamespaces.'\Models';
	}
}
