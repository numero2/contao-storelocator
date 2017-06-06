StoreLocator
======================

About
--

This extension offers the possibility to create multiple lists containing address data as well as opening times and contact data.

From the frontend the user can enter any possible address to see which stores are nearby (like on Google Maps).


Screenshot
--

![Editing a single store](https://cloud.githubusercontent.com/assets/17873830/26825303/ce9a11f2-4ab4-11e7-8abc-7f31a6cbf6b8.jpg)


System requirements
--

* [Contao 4](https://github.com/contao/core) or newer
* [Google API Key](https://github.com/numero2/contao-storelocator/wiki/Google-Keys)


Installation & Configuration
--

* Create a folder named `storelocator` in `system/modules`
* Clone this repository into the new folder
* Open `app/AppKernel.php` and add the following line to the $bundles array
  ```php
  new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle('storelocator', $this->getRootDir())
  ```
* Obtain an GoogleMaps API key and enter it into the System Settings under `StoreLocator`
* Run a database update via the Installtool
