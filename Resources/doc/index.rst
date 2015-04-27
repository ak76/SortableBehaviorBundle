Sortable behavior in admin listing
==================================

This is a full working example of how to implement a sortable feature in your Sonata admin listing

Background
----------

`A sortable behavior <http://sonata-project.org/bundles/doctrine-orm-admin/master/doc/reference/form_field_definition.html#advanced-usage-one-to-many>`_ is already available for one-to-many relationships.
However there is no packaged solution to have some up and down arrows to sort
your records such as showed in the following screen.

.. figure:: https://github.com/sonata-project/SonataAdminBundle/blob/master/Resources/doc/images/admin_sortable_listing.png
   :align: center
   :alt: Sortable listing
   :width: 700px


Pre-requisites
--------------

- you already have SonataAdmin and DoctrineORM up and running
- you already have an Entity class for which you want to implement a sortable feature.
  For the purpose of the example we are going to call it ``Client``.
- you already have an Admin set up, in this example we will call it ``ClientAdmin``
- you already have gedmo/doctrine-extensions bundle in your project (check stof/doctrine-extensions-bundle
  or knplabs/doctrine-behaviors for easier integration in your project) with the sortable
  feature enabled

The recipe
----------

First of are going to add a position field in our ``Client`` entity.

.. code-block:: php

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;



In ``ClientAdmin`` our we are going to add in the ``configureListFields`` method
a custom action and use the default twig template provided in the Ak76SortableBehaviorBundle

.. code-block:: php

	$listMapper
	    ->add('_action', 'actions', array(
            'actions' => array(
                'move' => array('template' => 'Ak76SortableBehaviorBundle:Sort/bootstrap2:_sort.html.twig')
            )
        ));



(There are two template versions. First is for Bootstrap v2 and second is for Bootstrap v3)
In order to add new routes for these actions we are also adding the following method where you should set variable ``property`` which represents the name of the position field of our ``Client`` entity.

.. code-block:: php

	protected function configureRoutes(RouteCollection $collection)
	{
	    $collection->add('move', $this->getRouterIdParameter() . '/move/{move}/{property}', [], ['move' => 'up|down|top|bottom', 'property' => 'position']);
	}



Now you can update your ``services.yml`` to use the handler provider by the PixSortableBehaviorBundle

.. code-block:: yaml

	services:
	    acme.admin.client:
	        class: Acme\DemoBundle\Admin\ClientAdmin
	        tags:
	            - { name: sonata.admin, manager_type: orm, label: "Clients" }
	        arguments:
	            - ~
	            - Acme\DemoBundle\Entity\Client
	            - 'Ak76SortableBehaviorBundle:SortableAdmin' # define the new controller via the third argument
	        calls:
	            - [ setTranslationDomain, [AcmeDemoBundle]]


Here is the example of the ``ClientAdmin``.

.. code-block:: php

   class ClientAdmin extends Admin
   {
    /** @var PositionHandler */
    private $positionService;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    );

    /**
     * @return PositionHandler
     */
    public function getPositionService()
    {
        return $this->positionService;
    }

    /**
     * @param PositionHandler $positionHandler
     */
    public function setPositionService(PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('enabled')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'move' => array('template' => 'Ak76SortableBehaviorBundle:Sort/bootstrap2:_sort.html.twig')
                )
            ));
    }

And in the services.yml add the following call

.. code-block:: yaml

    - [ setPositionService, [@ak76.sortable_behavior.position_handler]]


You should now have in your listing a new action column with 4 arrows to sort your records.

Enjoy ;)
