Vardius - List Bundle
======================================

Custom template
----------------
1. [Edit list view template](#edit-list-view-template)

### Edit list view template

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
                
            /* More Code*/

            return $listView;
        }

    }
```
