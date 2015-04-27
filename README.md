# SortableBehaviorBundle

Provides a way to sort records in Sonata Admin

## Installation

### Step 1: Install SortableBehaviorBundle

Add the following dependency to your composer.json file:

``` json
{
    "require": {
        "ak76/sortable-behavior-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ak76/sortable-behavior-bundle"
        }
    ],
}
```

and then run

```bash
php composer.phar update ak76/sortable-behavior-bundle
```

### Step 2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Ak76\SortableBehaviorBundle\Ak76SortableBehaviorBundle(),
    );
}
```

## Usage

Usage example you can see [here](https://github.com/ak76/sortable-behavior-bundle/blob/master/Resources/doc/index.rst).
