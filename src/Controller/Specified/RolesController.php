<?php
namespace Cmis\Controller\Specified;

use Cmis\Controller\AbstractCmisController;

class RolesController extends AbstractCmisController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('cmis-folder', array(
            'controller' => 'cmis-folder',
            'action'     => 'index',
            'folder'     => '/roles',
            'references' => array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-roles","action"=>"create"]]]],
                'edit'   => ['params'=>["containers"=>["content"=>["controller"=>"cmis-roles","action"=>"edit","object"=>null]]]],
            ),
        ));
    }

    public function editAction()
    {
        $objectId = $this->resolveObjectIdParam('object', '/roles');
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
            'folderId'   => '/roles',
        ));
    }

    public function deleteAction()
    {
        $this->getServiceLocator()->get('modelManager')->get('roles')->deleteRole($this->params('role'));
        return $this->redirect()->toReferer();
    }


    public function usersAction()
    {
        $serviceManager = $this->getServiceLocator();

        $form  = $serviceManager->get('FormElementManager')->get('KernelAdmin\Form\Roles\Users');
        $role  = $serviceManager->get('modelManager')->get('roles')->getRole($this->params('role'));

        if ($form->isPostAndValid()) {
            if (isset($form->getCommands()['save'])) {
                $users = $form->getData();
                $serviceManager->get('modelManager')->get('roles')->updateUsers($users['users'], $role['id']);
                return $this->redirect()->toRefresh();
            }
        } else {
            $form->setData(array(
                'users' => $serviceManager->get('modelManager')->get('roles')->getUsers($role['id'])->toArray(),
            ));
        }

        return array(
            'role' => $role,
            'form' => $form,
        );
    }
}
