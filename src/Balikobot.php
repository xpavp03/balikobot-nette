<?php

declare(strict_types = 1);

namespace Halasz\Balikobot;

use Nette\SmartObject;

/**
 * Class Balikobot
 *
 * @package Halasz\Balikobot
 */
class Balikobot
{
	use SmartObject;

	public function __construct(
		private string $apiUser,
		private string $apiKey,
	) {	}

	public function create(?string $shipper = null): BalikobotBuilder
	{
		$retVal = new BalikobotBuilder($this->apiUser, $this->apiKey);

		if ($shipper) {
			$retVal->shipper = $shipper;
		}

		return $retVal;
	}
}
