Vardius - List Bundle
======================================

Installation
----------------
1. Download using composer
2. Enable the VardiusListBundle
3. Add assets to you layout

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

``` yml
    vardius_list:
        title: List //default 'List'
        limit: 10   //default 10
        paginator: true //turn on/off paginator
```

You can also provide your custom value for list by setting them in provider class

### 1. Add assets to you layout
Include javascript for list view

``` html
    <script src="{{ asset('bundles/vardiuslist/js/list.js') }}"></script>
```

