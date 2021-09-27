# Balikobot API for NetteFW

This extension provides Nette implementation for library [inspirum/balikobot](https://packagist.org/packages/inspirum/balikobot)

## Installation

### Download
The best way to install Halasz/BalikobotNette is using Composer:
```sh
$ composer require halasz/balikobot-nette
```
### Registering
You can enable the extension using your neon config:
```sh
extensions:
	BalikobotNette: Halasz\Balikobot\BalikobotExtension
```
### Injecting
You can simply inject factory in Your Presenters/Services:
```php
public function __construct(Halasz\Balikobot\Balikobot $balikobot)
{
    parent::__construct();
    ...
}
```

## Usage

### Create packages and order shipment

```php
// First you need to do is create new BalikobotBuilder Instance:
$balikobotBuilder = $this->balikobot->create(Inspirum\Balikobot\Definitions\Shipper::CP);
// On the BalikobotBuilder you can create packages like this (For all $package methods you need to visit inspirum/balikobot documentation)
$package = $balikobotBuilder->createPackage();
$package->setServiceType(Inspirum\Balikobot\Definitions\ServiceType::CP_NP);
$package->setRecName('Josef Novák');
$package->setRecZip('11000');
$package->setRecCountry(Inspirum\Balikobot\Definitions\Country::CZECH_REPUBLIC);
$package->setRecPhone('776555888');
$package->setCodPrice(1399.00);
$package->setCodCurrency(Inspirum\Balikobot\Definitions\Currency::CZK);
// Or you can past package informations by factory parameter
$package = $balikobotBuilder->createPackage([
    'recName' => 'Josef Novák',
    'codPrice' => 1399.00,
]);


// And then you can add that package back to the builder
$balikobotBuilder->addPackage($package);
// Or you can past information about the package directly
$balikobotBuilder->addPackage([
    'recName' => 'Josef Novák',
    'codPrice' => 1399.00,
]);


// Then you need to send those packages to balikobot server (For all $orderedPackageCollection methods you need to visit inspirum/balikobot documentation)
$orderedPackageCollection = $balikobotBuilder->confirmPackages();
// Alternatively you can confirm $orderedPackageCollection and send packages to balikobot like this.
$orderedPackageCollection = $balikobotBuilder->getOrderPackageCollection();


// When you have completed work on $orderedPackageCollection, then you need to call (For all $orderedShipment methods you need to visit inspirum/balikobot documentation)
$orderedShipment = $balikobotBuilder->order($orderedPackageCollection);
// If you didn't change the $orderedPackageCollection then you can simply call
$orderedShipment = $balikobotBuilder->order();


// So very simple usage could look like this:
$balikobotBuilder = $this->balikobot->create(Shipper::CP);
$balikobotBuilder->addPackage([
    'recName' => 'Josef Novák',
    'codPrice' => 1399.00,
]);
$balikobotBuilder->order(); // In this case is internally called ->confirmPackages()
```


### Getting Inspirum\Balikobot\Services\Balikobot

```php
// When you need to track packages, remove them etc. You need to get Original balikobot class, which contains all necessary methods:
// (For all $balikobot methods you need to visit inspirum/balikobot documentation)
$inspirumBalikobot = $balikobotBuilder->getBalikobot();
````

For testing purposes, you can use these credentials:

- **API username:** `balikobot_test2cztest`
- **API key:** `#lS1tBVo`


## Configuration

All config is **required**.

Configuration must be specified in config file:
```sh
BalikobotNette:
	apiUser: 'balikobot_test2cztest'
	apiKey: '#lS1tBVo'
```


## Conclusion

This extension requires Nette3 and it is property of Tomas Halász © 2021
