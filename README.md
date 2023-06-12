Algolia Search Inventory for Magento 2.3.x and 2.4.x
==================

![Latest version](https://img.shields.io/badge/latest-1.0.5-green)

This Algolia_AlgoliaSearchInventory is a community-developed module to provide compatibility between Magento (2.3.x, 2.4.x) Inventory feature and Algolia Search 1.12+ extension. Though Algolia is a contributor to this repository, there is no product roadmap for this module and it’s not aligned with the Algolia/Magento integration product releases.

#### Compatibility

| Extension Version | Algolia Search Magento2                                                        |
|-------------------|---------------------------------------------------------------------------|
| v1.x              | [<=3.8.1](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.8.1) |
| >= v1.0.3            | [3.9.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.9.0)|
| >= v1.0.5            | [3.10.3](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.10.3)|


Installation
------------

The easiest way to install the extension is to use [Composer](https://getcomposer.org/)

Run the following commands:

- ```$ composer require algolia/algoliasearch-inventory-magento-2```
- ```$ bin/magento module:enable Algolia_AlgoliaSearchInventory```
- ```$ bin/magento setup:upgrade && bin/magento setup:static-content:deploy```
