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

### Available filters

`date` - available options: `['filed' => 'field name', 'condition' => 'eq|neq|lt|lte|gt|gte']`

`text` - available options: `['filed' => 'field name']`

`numeric` - available options: `['filed' => 'field name', 'condition' => 'eq|neq|lt|lte|gt|gte']`

`entity` - available options: `['filed' => 'field name', 'property' => 'property name', 'joinType' => 'join|leftJoin|innerJoin']`
