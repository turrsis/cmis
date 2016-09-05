<?php
namespace Tursis\Cmis\Controller\Specified;

use Tursis\Cmis\Controller\AbstractCmisController;

class UsersController extends AbstractCmisController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('cmis-folder', array(
            'controller' => 'cmis-folder',
            'action'     => 'children',
            'folder'     => '/users',
            'references' => array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-users","action"=>"create"]]]],
                'edit'   => ['params'=>["containers"=>["content"=>["controller"=>"cmis-users","action"=>"edit","object"=>null]]]],
            ),
        ));
    }

    public function deleteAction()
    {
        $userName = $this->params('user');
        $this->getUsersModel()->delete($userName);
        return $this->redirect()->toReferer();
    }

    public function createAction()
    {
        return $this->forward()->dispatch('cmis-object', array(
            'controller' => 'cmis-object',
            'action'     => 'create',
            'folderId'   => '/users',
        ));
    }

    public function editAction()
    {
        $objectId = $this->resolveObjectIdParam('object', '/users');
        return $this->forward()->dispatch('cmis-object', array(
            'controller' => 'cmis-object',
            'action'     => 'edit',
            'object'     => $objectId,
        ));
    }

    public function rolesAction()
    {
        $userName = $this->params('user');
        $user = $this->getUsersModel()->getUser($userName);
        $form = $this->getFormElement('user-roles');
        if ($form->isPost()) {
            $data = $form->getPost();
            $form->setData($data);
            if ($form->isPost('save') && $form->isValid()) {
                $data = $form->getData();
                $this->getUsersModel()->updateUserRoles($user, $data['roles']);
                return $this->redirect()->refresh();
            }
        } else {
            $roles = $this->getUsersModel()->getUserRoles($userName);
            $form->setData(array(
                'roles' => $roles,
            ));
        }
        $form->prepareElement();
        return array(
            'form'  => $form,
            'user'  => $user,
        );
    }

}
