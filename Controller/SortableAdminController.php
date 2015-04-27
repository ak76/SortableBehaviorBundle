<?php

namespace Ak76\SortableBehaviorBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SortableAdminController extends CRUDController
{
    /**
     * Move element
     *
     * @param Request $request
     * @param $id
     * @param $move
     * @param $property
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function moveAction(Request $request, $id, $move, $property)
    {
        $page                 = $request->get('page');
        $per_page             = $request->get('per_page');
        $filters              = $this->admin->getFilterParameters();
        $filters['_page']     = $page;
        $filters['_per_page'] = $per_page;
        $object               = $this->admin->getObject($id);

        $this->get('ak76.sortable_behavior.position_handler')->updatePosition($object, $property, $move);
        $this->admin->update($object);

        if ($this->isXmlHttpRequest()) {
            return $this->renderJson([
                'result' => 'ok',
                'objectId' => $this->admin->getNormalizedIdentifier($object)
            ]);
        }

        $this->get('session')->getFlashBag()->set('sonata_flash_info', $this->get('translator.default')->trans('Position updated'));

        return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $filters]));
    }
}
