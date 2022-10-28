Algolia Search Inventory for Magento 2.3.x and 2.4.x
==================

![Latest version](https://img.shields.io/badge/latest-1.0.3-green)

This Algolia_AlgoliaSearchInventory is a community-developed module to provide compatibility between Magento (2.3.x, 2.4.x) Inventory feature and Algolia Search 1.12+ extension. Though Algolia is a contributor to this repository, there is no product roadmap for this module and it’s not aligned with the Algolia/Magento integration product releases.


Installation
------------

The easiest way to install the extension is to use [Composer](https://getcomposer.org/)

Run the following commands:

- ```$ composer require algolia/algoliasearch-inventory-magento-2```
- ```$ bin/magento module:enable Algolia_AlgoliaSearchInventory```
- ```$ bin/magento setup:upgrade && bin/magento setup:static-content:deploy```
