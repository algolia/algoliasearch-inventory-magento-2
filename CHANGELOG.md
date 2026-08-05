# CHANGE LOG

## 1.4.1
- Fixed `Zend_Db_Select_Exception` ("correlation name 'stock_status_index' defined more than once")
  when a Recommend widget renders on the storefront cart page. The MSI stock filter now sets the
  `has_stock_status_filter` collection flag so Magento's frontend `add_stock_information` plugin
  does not add a second join for the same alias.
- Constrained the Algolia_AlgoliaSearch dependency to `>=3.16.3 <3.17.0 || >=3.17.3 <3.19.0`, 
  preserving the existing 3.16.3/3.17.3 minimums and adding an upper bound. Version 3.19.0 changed
  `Service\Product\RecordBuilder::__construct()` to require two additional arguments, which this
  module's `InventoryProductRecordBuilder` does not forward. Use 1.5.0 or later with 3.19.0+.

## 1.4.0
- Fix compatibility with versions 3.16.3 and 3.17.3 of the Algolia_AlgoliaSearch module 

## 1.1.0
- Fix compatibility with version 3.14.0 of the Algolia_AlgoliaSearch module (thanks @thomas-kl1)

## 1.0.4
- Fixed in_stock status issue

## 1.0.3

- Fixed the compatibility issue with magento version 2.4.5

## 1.0.2

- Updated Readme and dependencies

## 1.0.1

- Updated composer dependencies

## 1.0.0

- Release
