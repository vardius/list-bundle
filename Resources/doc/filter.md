Vardius - List Bundle
======================================

List Filters
----------------
1. [Creating filter provider](#creating-filter-provider)
2. [Available filters](#available-filters)

### Creating filter provider

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
            ->addFilter('dateTo', DateType::class, [
                'field' => 'date',
            ])
            ->addFilter('dateFrom', function (FilterEvent $event) {
                $queryBuilder = $event->getQueryBuilder();
                
                //Doctrine example
                $expression = $queryBuilder->expr();
                $queryBuilder
                    ->andWhere($expression->gte($event->getAlias().'.date', ':date'))
                    ->setParameter('date', $event->getValue());
                    
                //Propel example
                $queryBuilder->filterByDate(["min" => $event->getValue()])
                    
                //ElasticSearch Example
                $filter = $query->getFilter();
                $filter->addMust(new Range('date', ['lte' => '2014-11-14']));
                $query->setFilter($filter);
                
                return $queryBuilder;
            });
    }

}
```

### Available filters

`DateType` - available options: `['filed' => 'field name', 'condition' => 'eq|neq|lt|lte|gt|gte']`

`NullType` - available options: `['filed' => 'field name']`

`PropertyType` - available options: `['filed' => 'field name']`

`TextType` - available options: `['filed' => 'field name']`

`NumericType` - available options: `['filed' => 'field name', 'condition' => 'eq|neq|lt|lte|gt|gte']`

`EntityType` - available options: `['filed' => 'field name', 'property' => 'property name', 'joinType' => 'join|leftJoin|innerJoin']`
