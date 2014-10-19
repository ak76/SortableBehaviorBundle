<?php

namespace AK76\SortableBehaviorBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SortableAdminController extends CRUDController
{
    /**
     * Move element
     *
     * @param $id
     * @param $move
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function moveAction($id, $move)
    {
        $id         = $this->get('request')->get($this->admin->getIdParameter());
        $page       = $this->get('request')->get('page');
        $per_page   = $this->get('request')->get('per_page');
        $filters    = $this->admin->getFilterParameters();
        $filters['_page'] = $page;
        $filters['_per_page'] = $per_page;
        $object = $this->admin->getObject($id);

        $this->get('ak76_sortable_behavior.position')->updatePosition($object, $move);
        $this->admin->update($object);

        if ($this->isXmlHttpRequest()) {
            return $this->renderJson([
                'result' => 'ok',
                'objectId' => $this->admin->getNormalizedIdentifier($object)
            ]);
        }

        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->set('sonata_flash_info', $translator->trans('Position updated'));

        return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $filters]));
    }
}
