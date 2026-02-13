Contao StoreLocator
======================

[![](https://img.shields.io/packagist/v/numero2/contao-storelocator.svg?style=flat-square)](https://packagist.org/packages/numero2/contao-storelocator) [![License: LGPL v3](https://img.shields.io/badge/License-LGPL%20v3-blue.svg?style=flat-square)](http://www.gnu.org/licenses/lgpl-3.0)

About
--

This extension offers the possibility to create multiple lists containing address data, contact information as well as opening times. From the Frontend the user can enter any possible address to see which stores are nearby (like on Google Maps). [Read more](https://www.numero2.de/contao/erweiterungen/storelocator.html)

Screenshot
--

![Editing a single store](./docs/screenshot.png)

System requirements
--

* [Contao 5.3](https://github.com/contao/contao) or newer
* [Google API Key](https://github.com/numero2/contao-storelocator/wiki/Google-Keys)

Installation
--

* Install via Contao Manager or Composer (`composer require numero2/contao-storelocator`)
* Run a database update via the Contao-Installtool or using the [contao:migrate](https://docs.contao.org/dev/reference/commands/) command.

Using other providers
--

StoreLocator comes pre-bundled with a provider for Google Maps as well as OpenStreetMap Nominatim.
If you want to use another provider you'll need to install additional packages:

| Package                           | Provider                |
|-----------------------------------|-------------------------|
| `geocoder-php/bing-maps-provider` | Bing Maps               |
| `geocoder-php/here-provider`      | HERE Maps               |
| `geocoder-php/open-cage-provider` | OpenCage                |


Events
--

By default the importer will populate the model with the fields for the current row and the key provided in the first row of the file. For custom handling feel free to use the `contao.storelocator_store_import` event:

```php
// src/EventListener/StoreImportListener.php
namespace App\EventListener;

use numero2\StoreLocatorBundle\Event\StoreImportEvent;
use numero2\StoreLocatorBundle\Event\StoreLocatorEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(StoreLocatorEvents::STORE_IMPORT)]
class StoreImportListener {

    public function __invoke( StoreImportEvent $event ): void {
        // …
    }
}
```
