<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;


return (new Configuration())
    ->ignoreErrorsOnPackage('contao/manager-plugin', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    // ignore dependency as we want to install this, usage is checked
    ->ignoreErrorsOnPackage('geocoder-php/google-maps-provider', [ErrorType::UNUSED_DEPENDENCY])

    // ignore classes these will be checked during runtime
    // numero2/contao-tags
    ->ignoreUnknownClasses([
        'numero2\TagsBundle\TagsBundle',
        'numero2\TagsBundle\TagsRelModel',
    ])
;