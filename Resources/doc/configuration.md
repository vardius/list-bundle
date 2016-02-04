Vardius - List Bundle
======================================

Configuration
----------------
1. [Create your entity](#create-your-entity)
2. [Create ListViewProvider](#create-listviewprovider)
3. [Usage](#usage)
4. [Include scripts](#include-scripts)

### Create your entity

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

### Create ListViewProvider
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
#### YML
``` yml
    services:
        app.product.list_view:
            class: Lorenz\MainBundle\ListView\ProductProvider
```
#### XML
``` xml
    <service id="app.product.list_view" class="Lorenz\MainBundle\ListView\ProductProvider" parent="vardius_list.list_view.provider"/>
```

### Usage

In your action you can use list as follows:

``` php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/example")
 */
class OverlaysController extends Controller
{
    /**
     * @Route("/list", name="example_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository('AppBundle:Product');
        $listView = $this->get('app.product.list_view');
        $listDataEvent = new ListDataEvent($repository, $request);
        
        return [
            'list' => $listView->render($listDataEvent),
            'title' => $listView->getTitle(),
        ]
    }
```

In your views just display list using `|raw` filter

``` html
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<h1>{{ title }}</h1>
{{ list|raw }}
```

### Include scripts

For icons include styles in your view:

``` html
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
```

or get latest from

        [Bootstrap](http://getbootstrap.com/getting-started/#download)
        [Font Awesome](http://fortawesome.github.io/Font-Awesome/get-started/)

Advanced configuration
----------------
1. [Configure ListViewProvider](provider.md)
2. [List Filters](filter.md)
3. [Custom template](template.md)
