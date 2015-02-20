Vardius - List Bundle
======================================

List Bundle provides list view build

This is currently a work in progress.

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
6. Create view for your list or use [VardiusCrudBundle](https://github.com/Vardius/crud-bundle)

### 1. Download using composer
Install the package through composer:

    php composer.phar require vardius/list-bundle:*

### 2. Enable the VardiusListBundle
Enable the bundle in the kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\ListBundle\VardiusListBundle(),
        );
    }

### 3. Create your entity class

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

### 4. Create ListViewProvider class
Entity class

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

Service:

        <service id="app.product.list_view" class="Lorenz\MainBundle\ListView\ProductProvider" parent="vardius_list.list_view.provider"/>

### 5. Configure your ListViewProvider class
You can configure your list in the provider class, you can specify the limit of entries per page,
add columns of types (option, property, callable), add actions (global for page or row) and add filters to your list

Declare your filter form as a service:

        <service id="app.form.type.product_filter" class="AppBundle\Form\Type\Filter\ProductFilterType">
            <tag name="form.type" alias="product_filter"/>
        </service>

Create your provider class:

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
                ->setLimit(10) // set the entries per page
                ->addColumn('name', 'property') // add column
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
                        ->andWhere($alias.'.name = :name'))
                        ->setParameter('name', $name);

                    return $queryBuilder;
                })
                ->addAction('app.product_controller.list', 'Product List', 'fa-list');

                return $listView;
        }

    }

### 6. Create view for your list
Return to your view list data

    $params = [
        'data' => $listView->getData(new ListDataEvent($repository, $event->getRequest())),
        'listView' => $listView,
    ];

Set up your view for example:

    <div class="row">
        <div class="col-md-12">
            {% for action in listView.actions %}
                {% if loop.first %}
                    <div class="btn-group pull-right" role="group">
                {% endif %}
                <a href="{{ path(action.path) }}" class="btn btn-default" role="button">
                    {% if action.icon is not null %}
                        <i class="fa {{ action.icon }}"></i>
                    {% endif %}
                    {% if action.name is not null %}
                        {{ action.name }}
                    {% endif %}
                </a>
                {% if loo.last %}
                    </div>
                {% endif %}
            {% endfor %}
        </div>
    </div>
    {% set hasFilters = (listView.filterForms is not empty) %}
    <div class="row">
        <div class="col-md-{{ hasFilters ? '8' : '12' }}">
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    {% for column in listView.columns %}
                        <td>{{ column.property|upper }}</td>
                    {% endfor %}
                    {% if listView.rowActions is not empty %}
                        <td></td>
                    {% endif %}
                </thead>
                <tbody>
                {% for entity in data %}
                    <tr class='list-view-item'>
                        {% for column in listView.columns %}
                            <td>{{ column.getData(entity)|raw }}</td>
                        {% endfor %}
                    </tr>
                {% endfor %}
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-12">
                    {% set currentPage = listView.currentPage %}
                    {% set previousPage = (currentPage > 1 ? (currentPage - 1) : 1) %}
                    {% set lastPage = listView.lastPage %}
                    {% set nextPage = (currentPage < lastPage ? lastPage + 1 : lastPage) %}
                    <div class="paginator pull-right">
                        <a href="{{ path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')|merge({'page': previousPage}) ) }}">
                            <i class="fa fa-caret-square-o-left"></i>
                        </a>
                        {% if previousPage <  currentPage %}
                            <a href="{{ path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')|merge({'page': previousPage}) ) }}">{{ previousPage }}</a>
                        {% endif %}
                        <a href="{{ path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')|merge({'page': currentPage}) ) }}">{{ currentPage }}</a>
                        {% if nextPage >  currentPage %}
                            <a href="{{ path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')|merge({'page': nextPage}) ) }}">{{ nextPage }}</a>
                        {% endif %}
                        <a href="{{ path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')|merge({'page': nextPage}) ) }}"
                           class="last {% if lastPage == currentPage %} current{% endif %}">
                            <i class="fa fa-caret-square-o-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {% if hasFilters %}
            <div class="col-md-4">
                {% for form in listView.filterForms %}
                    {{ form(form) }}
                {% endfor %}
            </div>
        {% endif %}
    </div>

For icons include styles in your view:

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

or get latest from

        [Bootstrap](http://getbootstrap.com/getting-started/#download)
        [Font Awesome](http://fortawesome.github.io/Font-Awesome/get-started/)


RELEASE NOTES
==================================================
**0.1.0**

- First public release of list-bundle