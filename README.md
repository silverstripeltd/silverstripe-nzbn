# SilverStripe NZBN

SilverStripe module for adding a NZBN lookup field to a form.

## Requirements

For a CMS user to add the NZBN lookup field, you need to have SilverStripe User Forms module installed.

## Features

* A developer can add a lookup field to a custom form
* A site administrator can add a lookup field to a User Defined Form
* The lookup field can pre-populate any field that is available through the NZBN API 
* Can pre-populate Text or Dropdown fields

## Installation

```sh
$ composer require somar/silverstripe-nzbn
```

## Configuration
**mysite/\_config/nzbn.yml**
```yml
Somar\NZBN\Model\LookupField:
  button_text: 'Search'

Somar\NZBN\Service\LookupService:
  # url: 'https://api.business.govt.nz/sandbox/nzbn/v5'
  subscription_key: 'YOUR SUBSCRIPTION KEY'
```

## NZBN API Access

Follow the steps listed here on [api.business.govt.nz](https://portal.api.business.govt.nz/getting-started) to gain access to the NZBN API.

## Documentation

* [User Guide](docs/en/userguide.md)
* [Developer Documentation](docs/en/developer.md)
