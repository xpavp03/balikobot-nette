<?php

declare(strict_types = 1);

namespace Halasz\Balikobot;

use Inspirum\Balikobot\Contracts\ExceptionInterface;
use Inspirum\Balikobot\Definitions\Shipper;
use Inspirum\Balikobot\Model\Aggregates\OrderedPackageCollection;
use Inspirum\Balikobot\Model\Aggregates\PackageCollection;
use Inspirum\Balikobot\Model\Values\OrderedShipment;
use Inspirum\Balikobot\Model\Values\Package;
use Inspirum\Balikobot\Services\Balikobot as InspirumBalikobot;
use Inspirum\Balikobot\Services\Requester;
use Nette\SmartObject;

/**
 * Class BalikobotBuilder
 *
 * @package Halasz\Balikobot
 *
 * @property-write string $shipper
 */
class BalikobotBuilder
{
	use SmartObject;

	private ?PackageCollection $packageCollection = null;
	private ?Requester $requester = null;
	private ?InspirumBalikobot $balikobot = null;
	private ?OrderedPackageCollection $orderedPackageCollection = null;
	private ?string $shipper = null;

	public function __construct(
		private string $apiUser,
		private string $apiKey,
	) {	}

	/**
	 * Package factory
	 *
	 * @param array<string,mixed> $data
	 * @return Package
	 */
	public function createPackage(array $data = []): Package
	{
		return new Package($data);
	}

	/**
	 * @param string $shipper
	 */
	public function setShipper(string $shipper): void
	{
		$this->shipper = $shipper;
	}

	/**
	 * @param array|Package $package  It may be array or Package, but we prefer to be Package.
	 * @return $this
	 * @throws BalikobotException
	 */
	public function addPackage(array|Package $package): static
	{
		if (is_array($package)) {
			$package = $this->createPackage($package);
		}
		$this->getPackageCollection()->add($package);
		return $this;
	}

	/**
	 * @return OrderedPackageCollection
	 * @throws BalikobotException
	 * @throws ExceptionInterface
	 */
	public function confirmPackages(): OrderedPackageCollection
	{
		if ($this->orderedPackageCollection) {
			throw new BalikobotException('This packages is already confirmed.', 2);
		}

		$balikobot = $this->getBalikobot();
		$this->orderedPackageCollection = $balikobot->addPackages($this->packageCollection);
		return $this->orderedPackageCollection;
	}

	/**
	 * @return OrderedPackageCollection
	 * @throws BalikobotException
	 * @throws ExceptionInterface
	 */
	public function getOrderPackageCollection(): OrderedPackageCollection
	{
		if (!$this->orderedPackageCollection) {
			$this->orderedPackageCollection = $this->confirmPackages();
		}
		return $this->orderedPackageCollection;
	}

	/**
	 * @param OrderedPackageCollection|null $collection
	 * @return OrderedShipment
	 * @throws BalikobotException
	 * @throws ExceptionInterface
	 */
	public function order(OrderedPackageCollection $collection = null): OrderedShipment
	{
		if (!$collection) {
			$collection = $this->getOrderPackageCollection();
		}
		return $this->getBalikobot()->orderShipment($collection);
	}

	/**
	 * @return InspirumBalikobot
	 */
	public function getBalikobot(): InspirumBalikobot
	{
		if (!$this->balikobot) {
			$this->balikobot = new InspirumBalikobot($this->getRequester());
		}
		return $this->balikobot;
	}

	/**
	 * @return Requester
	 */
	private function getRequester(): Requester
	{
		if (!$this->requester) {
			$this->requester = new Requester($this->apiUser, $this->apiKey);
		}
		return $this->requester;
	}

	/**
	 * @return PackageCollection
	 * @throws BalikobotException
	 */
	private function getPackageCollection(): PackageCollection
	{
		if (!$this->packageCollection) {
			if (!$this->shipper) {
				throw new BalikobotException('You must set shipper first. Please use \'' . static::class . '->setShipper()\' method. Acceptable shippers can be found at \'' . Shipper::class, 1);
			}
			$this->packageCollection = new PackageCollection($this->shipper);
		}
		return $this->packageCollection;
	}
}
