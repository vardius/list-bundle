Vardius - List Bundle
======================================

List Bundle provides list view build

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/faea1bb4-c6ee-4be3-ab78-12e0eb19c11e/big.png)](https://insight.sensiolabs.com/projects/faea1bb4-c6ee-4be3-ab78-12e0eb19c11e)

ABOUT
==================================================
Contributors:

* [Rafał Lorenz](http://rafallorenz.com)

Want to contribute ? Feel free to send pull requests!

Have problems, bugs, feature ideas?
We are using the github [issue tracker](https://github.com/vardius/list-bundle/issues) to manage them.

HOW TO USE
==================================================

Installation
----------------
1. Download using composer
2. Enable the VardiusListBundle
3. Create your entity class
4. Create ListViewProvider class
5. Configure your ListViewProvider class
6. Edit list view template

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

### 3. Create your entity class

``` php
    /**
     * Product
     *
     * @ORM\Table(name="product")
     * @ORM\Entity
     */
    class Product
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         * @Assert\NotBlank()
         */
        private $name;

        // setters and getters...
    }
```

### 4. Create ListViewProvider class
Entity class

``` php
    <?php

    namespace App\DemoBundle\ListView;


    use Vardius\Bundle\ListBundle\Event\ListEvent;
    use Vardius\Bundle\ListBundle\ListView\ListView;
    use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProvider;

    class ProductProvider extends ListViewProvider
    {
        /**
         * Provides list view
         *
         * @return ListView
         */
        public function buildListView()
        {
            $listView = $this->listViewFactory->get();
            //...
        }

    }
```

Service:

``` xml
    <service id="app.product.list_view" class="Lorenz\MainBundle\ListView\ProductProvider" parent="vardius_list.list_view.provider"/>
```

### 5. Configure your ListViewProvider class
You can configure your list in the provider class, you can specify the limit of entries per page,
add columns of types (option, property, callable), add actions (global for page or row) and add filters to your list

Declare your filter form as a service:

``` xml
    <service id="app.form.type.product_filter" class="AppBundle\Form\Type\Filter\ProductFilterType">
        <tag name="form.type" alias="product_filter"/>
    </service>
```

Create your provider class:

``` php
    use Vardius\Bundle\ListBundle\Action\Action;

    class ProductProvider extends ListViewProvider
    {
        /**
         * Provides list view
         *
         * @return ListView
         */
        public function buildListView()
        {
            $listView = $this->listViewFactory->get();

            $listView
                ->setTitle('Page title') //set page title
                ->setLimit(10) // set the entries per page
                ->addColumn('name', 'property') // add column
                ->addColumn('checkbox', 'option')
                ->addColumn('custom', 'callable', [
                    'callback' => function(Product $product){
                          return 'custom value';
                      },
                ])
                ->addColumn('', 'action', [
                    'actions' => [
                        [
                            'path' => 'app.product_controller.edit',
                            'name' => 'edit',
                            'icon' => 'fa-edit',
                        ],
                    ],
                ])
                ->addFilter('product_filter', function (FilterEvent $event) {

                    $formData = $event->getData();
                    $queryBuilder = $event->getQueryBuilder();

                    $aliases = $queryBuilder->getRootAliases();
                    $alias = array_values($aliases)[0];

                    $name = $formData['name'];

                    $queryBuilder
                        ->andWhere($alias.'.name = :name')
                        ->setParameter('name', $name);

                    return $queryBuilder;
                })
                ->addAction('app.product_controller.list', 'Product List', 'fa-list');

            return $listView;
        }

    }
```

### 6. Edit list view template

``` php
    use Vardius\Bundle\ListBundle\Action\Action;

    class ProductProvider extends ListViewProvider
    {
        /**
         * Provides list view
         *
         * @return ListView
         */
        public function buildListView()
        {
            $listView = $this->listViewFactory->get();

            $listView
                ->setView('VardiusListBundle:List:list') //set custom list view template
                
            ...

            return $listView;
        }

    }
```

For icons include styles in your view:

``` html
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
```

or get latest from

        [Bootstrap](http://getbootstrap.com/getting-started/#download)
        [Font Awesome](http://fortawesome.github.io/Font-Awesome/get-started/)


RELEASE NOTES
==================================================
**0.1.0**

- First public release of list-bundle

**0.2.0**

- Major bug fix and updates
