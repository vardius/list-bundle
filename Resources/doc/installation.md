Vardius - List Bundle
======================================

Installation
----------------
1. Download using composer
2. Enable the VardiusListBundle

### 1. Download using composer
Install the package through composer:

``` bash
    php composer.phar require vardius/list-bundle:*
```

### 2. Enable the VardiusListBundle
Enable the bundle in the kernel:

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\ListBundle\VardiusListBundle(),
        );
    }
```

Add to config.yml:

``` yaml
    vardius_list:
        title: List //default 'List'
        limit: 10   //default 10
```

You can also provide your custom value for list by setting them in provider class
