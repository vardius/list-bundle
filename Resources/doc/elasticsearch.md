Vardius - List Bundle
======================================

List Filters
----------------
1. [Create filtered query](#create-filtered-query)
2. [Hook to result event](#hook-to-result-event)
3. [Paginate Results](#paginate-results)

First of all read [FOSElasticaBundle Documentation](https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/Resources/doc/index.md)

### Create filtered query

There are two ways you can create query in your action or in list view provider for more information visit [Configuration](configuration.md)

### Hook to result event

You have to hook to event in case of returning results there is a need to inject `finder`.

``` php

namespace AppBundle\EventListener;

use Elastica\Query;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Event\ListResultEvent;

class ListResultsSubscriber implements EventSubscriberInterface
{
    /**
     * ListResultsSubscriber constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ListEvents::RESULTS => 'onResults'
        );
    }

    public function onResults(ListResultEvent $event)
    {
        $query = $event->getQueryBuilder();
        
        //Check if it is ElasticSearch Query
        //Otherwise it could be Propel or DOctrine list action
        if ($query instanceof Query) {
            $finder = $this->$this->container('fos_elastica.finder.app.products');
            $this->results = $finder->find($query);
        }
    }
}
```

```yml
    app.list_results_subscriber:
      class: AppBundle\EventListener\ListResultsSubscriber
      arguments: ["@service_container"]
      tags:
          - { name: kernel.event_subscriber }
```

### Paginate Results

The `total` number elements for `elastic search` DB Driver will always show `0`.
