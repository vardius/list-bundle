Vardius - List Bundle
======================================

Configuration
----------------
1. Create your entity class
2. Create ListViewProvider class
3. Configure your ListViewProvider class
4. Available options for column types
5. Edit list view template

### 1. Create your entity class

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

### 2. Create ListViewProvider class
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

### 3. Configure your ListViewProvider class
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
                ->addColumn('name', 'property', [ // add column
                    'sort' => true, //enable colum sorting
                    'label' => 'My Label', //custom column label
                    'url' => [ //column as link
                        'path' => 'app.product_controller.show',
                        'parameters' => [], //entity id will be added automatically no need to put it here
                    ],
                    'attr' => [
                        'class' => 'custom class',
                        'styles' => 'font-weight: bold;'
                    ]
                ])
                ->addColumn('custom', 'callable', [
                    'callback' => function(Product $product){
                          return 'custom value';
                    },
                ])
                ->addColumn('date', 'date', [
                    'date_format' => 'm/d/Y' //date format
                    'callback' => function(Product $product){ //you can provide callback will override property value
                          return 'custom value';
                    },
                ])
                ->addColumn('image', 'image', [
                    'callback' => function(Product $product){ //you can provide callback will override property value
                          return 'custom value';
                    },
                ])
                ->addColumn('details', 'raw', [
                    'callback' => function(Product $product){ //you can provide callback will override property value
                          return 'custom html string';
                    },
                ])
                ->addColumn('checkbox', 'option')
                ->addColumn('', 'action', [
                    'actions' => [
                        [
                            'path' => 'app.product_controller.edit',
                            'name' => 'edit',
                            'icon' => 'fa-edit',
                            'parameters' => [],
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

### 4. Available options for column types

Property column: `attr, label, sort, url`
Callable column: `attr, label, sort, url, callback`
Date column: `attr, label, sort, url, callback, date_format`
Raw column: `attr, label, sort, url, callback`
Image column: `attr, label, sort, url, callback`
Action column: `attr, label, actions`
Option column: `attr, label`

### 5. Edit list view template

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
