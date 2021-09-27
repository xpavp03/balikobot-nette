<?php

declare(strict_types = 1);

namespace Halasz\Balikobot;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\Schema\Expect;

class BalikobotExtension extends CompilerExtension
{
	public function getConfigSchema(): Nette\Schema\Schema
	{
		return Expect::structure([
			'apiUser' => Expect::string()->required(),
			'apiKey' => Expect::string()->required(),
		])->castTo('array');
	}

	public function loadConfiguration(): void
	{
		$this->compiler->loadDefinitionsFromConfig(
			Helpers::expand(
				$this->loadFromFile(__DIR__ . '/config/common.neon')['services'],
				(array) $this->config,
			),
		);
	}
}
