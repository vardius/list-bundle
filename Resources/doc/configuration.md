Vardius - List Bundle
======================================

Configuration
----------------
1. Create your entity class
2. Create ListViewProvider class
3. Configure your ListViewProvider class
4. Available options for column types
5. Creating filter provider
6. Edit list view template

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
                ->addOrder('name', 'DESC') // set order for column
                ->setQueryBuilder($entityManager->getRepository('AppBundle:Product')->getCustomQueryBuilder()) //set custom query builder
                ->addColumn('name', 'property', [ // add column
                    'sort' => true, //enable colum sorting
                    'label' => 'My Label', //custom column label
                    'row_action' => [ //column as link
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
                ->addColumn('custom2', 'callable', [
                    'callback' => [$object, 'functionName'],
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
                ->addFilter('product_filter', function (ListFilterEvent $event) {

                    $formData = $event->getData();
                    $queryBuilder = $event->getQueryBuilder();
                    $alias = $event->getAlias();

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

Property column: `attr, label, sort, row_action`
Callable column: `attr, label, sort, row_action, callback`
Date column: `attr, label, sort, row_action, callback, date_format`
Raw column: `attr, label, sort, row_action, callback`
Image column: `attr, label, sort, row_action, callback`
Action column: `attr, label, actions`
Option column: `attr, label`

### 5. Creating filter provider

There are multiple options to apply filter to your list, first of them you may already know. It was presented before in this documentaction.

``` php
        ...
        
        public function buildListView()
        {
            $listView = $this->listViewFactory->get();

            $listView
                ...
                ->addFilter('product_filter', function (ListFilterEvent $event) {

                    $formData = $event->getData();
                    $queryBuilder = $event->getQueryBuilder();
                    $alias = $event->getAlias();

                    $name = $formData['name'];

                    $queryBuilder
                        ->andWhere($alias.'.name = :name')
                        ->setParameter('name', $name);

                    return $queryBuilder;
                });

            return $listView;
        }
        
        ...
    }
```

You can also create filter provider for the filter form as follow.

``` php
        ...
       
        public function buildListView()
        {
            $listView = $this->listViewFactory->get();

            $listView
                ...
                ->addFilter('event_filter', 'app.product.filter_provider'); //service id of your provider

            return $listView;
        }
        
        ...
    }
```

Method addFilter accept values: `form field name` and `filter type class`. Filter type class can by passed as new class instance or by name.
You can also pass `callback` instead of class type.
Provider class example:

``` php
class FilterProvider extends \Vardius\Bundle\ListBundle\Filter\Provider\FilterProvider
{
    /**
     * @inheritDoc
     */
    public function build()
    {
        $this
            ->addFilter('dateFrom', new DateType()); //you can pass name of filter or pass it by new ClassType() declaration
            ->addFilter('dateTo', 'date', [
                'field' => 'date',
            ])
            ->addFilter('dateFrom', function (FilterEvent $event) {
                $queryBuilder = $event->getQueryBuilder();
                $expression = $queryBuilder->expr();
                
                $queryBuilder
                    ->andWhere($expression->gte($event->getAlias().'.date', ':date'))
                    ->setParameter('date', $event->getValue());
                
                return $queryBuilder;
            });
    }

}
```

Available filters:

`date` - available options: `['filed' => 'field name', 'condition' => 'gte|gt|lte|lt']`
`text` - available options: `['filed' => 'field name']`

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
