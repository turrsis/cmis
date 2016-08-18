<?php
namespace Cmis\Controller\Specified;

use Cmis\Controller\AbstractCmisController;

class NavigationController extends AbstractCmisController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('cmis-folder', array(
            'controller' => 'cmis-folder',
            'action'     => 'index',
            'folder'     => '/menus',
            'references' => array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-navigation","action"=>"create"]]]],
                'edit'   => ['params'=>["containers"=>["content"=>["controller"=>"cmis-navigation","action"=>"edit","object"=>null]]]],
            ),
        ));
    }

    public function editAction()
    {
        $objectId = $this->resolveObjectIdParam('object', '/menus');
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
            'folderId'   => '/menus',
        ));
    }

    public function deleteAction()
    {
        $this->getServiceLocator()->get('modelManager')->get('roles')->deleteRole($this->params('role'));
        return $this->redirect()->toReferer();
    }
}
