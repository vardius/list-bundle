Vardius - List Bundle
======================================

Configure ListViewProvider
----------------
1. [Provide custom config](#provide-custom-config)
2. [Available options for column types](#available-options-for-column-types)

### Provide custom config

You can configure your list in the provider class, you can specify the limit of entries per page,
add columns of types (option, property, callable), add actions (global for page or row) and add filters to your list

Declare your filter form as a service:
###### YML
``` yml
services:
    app.form.type.product_filter:
        class: AppBundle\Form\Type\Filter\ProductFilterType
        tags:
            - { name: form.type, alias: product_filter }
```

###### XML
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
                ->setDbDriver('orm') //available orm, propel and elasticsearch, default from bundle config
                ->setTitle('Page title') //set page title
                ->setLimit(10) // set the entries per page
                ->addOrder('name', 'DESC') // set order for column
                ->setQuery($entityManager->getRepository('AppBundle:Product')->getCustomQueryBuilder()) //set custom query builder, model criteria or elastic search filtered query
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

                    //Doctrine example
                    $queryBuilder
                        ->andWhere($alias.'.name = :name')
                        ->setParameter('name', $name);
                        
                    //Propel example
                    $queryBuilder->filterByName($name);
                    
                    //ElasticSearch Example
                    $filter = $query->getFilter();
                    $fieldQuery = new \Elastica\Query\Match();
                    $fieldQuery->setFieldQuery('title', 'I am a title string');
                    $filter->addShould($fieldQuery);
                    $query->setFilter($filter);

                    return $queryBuilder;
                })
                ->addAction('app.product_controller.list', 'Product List', 'fa-list');

            return $listView;
        }

    }
```

### Available options for column types

Property column: `attr, label, sort, row_action`

Callable column: `attr, label, sort, row_action, callback`

Date column: `attr, label, sort, row_action, callback, date_format`

Raw column: `attr, label, sort, row_action, callback`

Image column: `attr, label, sort, row_action, callback`

Action column: `attr, label, actions`

Option column: `attr, label`
