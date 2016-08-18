<?php
namespace Cmis\Controller\Specified;

use Cmis\Controller\AbstractCmisController;

class PagesController extends AbstractCmisController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('cmis-folder', array(
            'controller' => 'cmis-folder',
            'action'     => 'index',
            'folder'     => '/pages',
            'references' => array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-pages","action"=>"create"]]]],
                'edit'   => ['params'=>["containers"=>["content"=>["controller"=>"cmis-pages","action"=>"edit","object"=>null]]]],
            ),
        ));
    }

    public function editAction()
    {
        $objectId = $this->resolveObjectIdParam('object', '/pages');
        return $this->forward()->dispatch('cmis-object', array(
            'controller' => 'cmis-object',
            'action'     => 'edit',
            'object'     => $objectId,
        ));
    }

    public function createAction()
    {
        return $this->forward()->dispatch('cmis-object', array(
            'controller' => 'cmis-object',
            'action'     => 'create',
            'folderId'   => '/pages',
        ));
    }

    public function deleteAction()
    {
        $this->getServiceLocator()->get('modelManager')->get('roles')->deleteRole($this->params('role'));
        return $this->redirect()->toReferer();
    }
}
