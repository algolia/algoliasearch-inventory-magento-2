# AGENTS.md

## What this module is

`Algolia_AlgoliaSearchInventory` adapts the Algolia Search extension for Magento 2
(`Algolia_AlgoliaSearch`) to Magento Multi-Source Inventory. It replaces the core extension's
legacy CatalogInventory stock reads with MSI equivalents, so indexed stock status and quantity
reflect salable stock for the store's sales channel, and it triggers reindexing when inventory
source items change.

It is a compatibility shim, not a feature module: no configuration, no schema, no frontend assets.

## Change policy

Changes here are compatibility-driven only. Keep them minimal and local. Do not restructure the
module, rename classes, change the DI strategy, or refactor the seams it shares with the core
extension. There is no product roadmap for this module (see `README.md`), so a change not required
to keep it working against a given core extension release is out of scope.

## Compatibility

This module is coupled to the core extension's internals and is released in lockstep with it.
Version numbers are deliberately not restated here. The sources of truth are:

- `README.md`: the authoritative core-extension-to-inventory-version compatibility matrix
- `composer.json`: the module `version` and its dependency constraints
- `etc/module.xml`: `setup_version`

A release must update all three in lockstep: `composer.json` `version`, `etc/module.xml`
`setup_version`, and the `README.md` badge plus a new matrix row.

## When the core extension changes

Two classes are bound by `<preference>` in `etc/di.xml` and extend their core counterparts,
re-declaring and forwarding the parent's full constructor argument list:

- `Helper/InventoryProductHelper.php` → `Algolia\AlgoliaSearch\Helper\Entity\ProductHelper`
- `Service/Product/InventoryProductRecordBuilder.php` → `Algolia\AlgoliaSearch\Service\Product\RecordBuilder`

A constructor signature change in either core parent is a fatal `TypeError` during reindex, not a
test failure, and nothing in this repository will catch it. The most recent `CHANGELOG.md` entry is
a case in point. Before any other work on a core version bump:

1. Diff both core parent constructors against the subclass constructors and mirror any change,
   forwarding every argument.
2. Diff the overridden methods (`addStockFilter`, `addStockQty`) and the plugged methods
   (`RecordBuilder::productIsInStock`, `RecordBuilder::addInStock`) against the new core version.
3. Confirm every plugin target named in `etc/di.xml` still exists in core under the same name.
   A `disabled="true"` entry naming a plugin core has renamed is a silent no-op.

## Deliberate DI state

`etc/di.xml` contains entries that look like cruft and must not be removed as cleanup:

- **`algoliaStockItems` is disabled.** This is the core extension's own legacy stock-item plugin.
  Current core releases disable it too, making this redundant today, but it is retained
  deliberately: if core re-enables it, MSI installs would regress to duplicate reindex queuing.
- **`algoliaInventorySourceItemsSave` is registered but disabled.** It was reported to enqueue
  duplicate reindex jobs, duplicating work already triggered elsewhere. Treat this as an open
  question rather than a settled design. The original investigation did not resolve the frontend
  out-of-stock path, and core queue behaviour has changed substantially since. Verify current
  behaviour before re-enabling, removing, or building on it.
- **`algoliaInventorySourceItemsDelete` is enabled**, because source-item deletions are not
  otherwise caught. Both source-item plugins share `Plugin/InventoryApi/AbstractSourceItemsPlugin.php`.

## Testing

Unit tests live in `Test/Unit` and extend `Algolia\AlgoliaSearch\Test\TestCase`, a base class from
the core extension, so they cannot run without it installed. Run them from the Magento root
against the installed path, not from this directory:

```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist \
  vendor/algolia/algoliasearch-inventory-magento-2/Test/Unit
```

Integration coverage requires a Magento instance with MSI modules enabled.

There is no CI in this repository; verification is local.

## Verification without a Magento environment

- `php -l <file>`: syntax-check modified PHP files
- `composer validate`: check `composer.json`
- Confirm DI wiring in `etc/di.xml` when adding or renaming classes
- Confirm PSR-4 alignment: namespace root `Algolia\AlgoliaSearchInventory` maps to the repository
  root (`composer.json` autoload, `registration.php`)

`composer.json` declares only `magento/module-inventory-api`, while the code also uses
`Magento\InventoryCatalog`, `Magento\InventoryCatalogApi`, and `Magento\InventorySalesApi`. These
arrive transitively in practice. Do not change dependency constraints as a drive-by fix.
