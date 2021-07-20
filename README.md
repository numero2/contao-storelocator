Contao StoreLocator
======================

[![](https://img.shields.io/packagist/v/numero2/contao-storelocator.svg?style=flat-square)](https://packagist.org/packages/numero2/contao-storelocator) [![License: LGPL v3](https://img.shields.io/badge/License-LGPL%20v3-blue.svg?style=flat-square)](http://www.gnu.org/licenses/lgpl-3.0)

About
--

This extension offers the possibility to create multiple lists containing address data, contact information as well as opening times.

From the frontend the user can enter any possible address to see which stores are nearby (like on Google Maps).


Screenshot
--

![Editing a single store](./docs/screenshot.png)


System requirements
--

* [Contao 4.4](https://github.com/contao/core) or newer
* [Google API Key](https://github.com/numero2/contao-storelocator/wiki/Google-Keys)

Installation
--

* Install via Contao Manager or Composer (`composer require numero2/contao-storelocator`)
* Run a database update via the Contao-Installtool or using the [contao:migrate](https://docs.contao.org/dev/reference/commands/) command.

Using other providers
--

StoreLocator comes pre-bundled with a provider for Google Maps.
If you want to use another provider you'll need to install additional packages:

| Package                           | Provider                     |
| --------------------------------- | ---------------------------- |
| `geocoder-php/bing-maps-provider` | Microsoft Bing Maps          |
| `geocoder-php/here-provider`      | Here Maps                    |
| `geocoder-php/nominatim-provider` | OpenStreetMap Nominatim Maps |
| `geocoder-php/open-cage-provider` | OpenStreetMap OpenCage Maps  |

