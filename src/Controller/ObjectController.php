<?php
namespace Cmis\Controller;

use Cmis\Form\Object\AbstractObject as AbstractCmisObjectForm;

class ObjectController extends AbstractCmisController
{
    public function createAction()
    {
        $formName = 'cmisCreateObject';
        $data = $this->getRequest()->getPost($formName, array());
        $repository = $this->getRepository();
        $form = AbstractCmisObjectForm::factory($this->getServiceLocator(), $repository, array(
            'name'         => $formName,
            'type'         => isset($data['properties']['cmis:objectTypeId']) ? $data['properties']['cmis:objectTypeId'] : 'cmis:document',
            'parentFolder' => $this->params('folderId'),
            'isCreate'     => true,
            'permissions'  => array(
                'cmis:parentId.update'     => $this->params('folderId') === null,
                'cmis:objectTypeId.update' => true,
            ),
        ));
        $form->setData($data);

        if (isset($data['save']) && $form->isValid()) {
            $data = $form->getData();
            $properties = $data['properties'];
            unset($data['properties']);
            $objectId = $repository->getObjectService()->createObject($properties, $data);

            return $this->redirect()->toRoute(null, ["containers"=>["content"=>array(
                "controller" => "cmis-object",
                "action"     => "edit",
                "object"     => $objectId
            )]]);
        }
        $messages = $form->getMessages();
        $form->setWrapElements(true)->prepare();
        return array(
            'form'   => $form,
        );
    }

    public function editAction()
    {
        $repository = $this->getRepository();
        $objectId   = $this->params('object');
        $object     = $repository->getObjectService()->getObject($objectId, array(
            'includeRelationships' => array(
                'relationshipDirection'       => 'target',
                'typeId'                      => 'cmis:folder',
                'includeSubRelationshipTypes' => true,
            ),
            'includePolicyIds' => true,
        ));

        $form = AbstractCmisObjectForm::factory($this->getServiceLocator(), $repository, array(
            'name'         => 'cmisEditObject',
            'type'         => $object['properties']['cmis:objectTypeId'],
            'parentFolder' => isset($object['properties']['cmis:parentId']) ? $object['properties']['cmis:parentId'] : $object['folderId'],
            'isCreate'     => false,
            'permissions'  => array(
                'cmis:parentId.update'     => true,
                'cmis:objectTypeId.update' => true,
            ),
        ));

        if (!$this->getRequest()->isPost()) {
            $form->setData($object);
        } else {
            $post = $this->getRequest()->getPost();
            if (isset($post[$form->getName()])) {
                $post = $post[$form->getName()];
                $post['properties'] = array_replace($object['properties'], $post['properties']);
                $form->setData($post);

                if (isset($post['save']) && $form->isValid()) {
                    $data = $form->getData();
                    if (isset($data['folderId']) && $data['folderId']) {
                        $data['folderId'] = array(
                            'add'    => array_diff($data['folderId'],   $object['folderId']),
                            'remove' => array_diff($object['folderId'], $data['folderId']),
                        );
                    }
                    $repository->getObjectService()->updateObject($data);
                    $this->redirect()->toReferer();
                }
                $messages = $form->getMessages();
            }

        }
        $form->setWrapElements(true)->prepare();
        return array(
            'object' => $object,
            'form'   => $form,
        );
    }

    protected function getObjectForEdit($objectId)
    {
        $cmisRepository = $this->getServiceLocator()->get('cmis:Repository');
        $objectService       = $cmisRepository->getObjectService();
        $repositoryService   = $cmisRepository->getRepositoryService();
        $relationshipService = $cmisRepository->getRelationshipService();

        $options = array(
            'includeRelationships' => array(
                'relationshipDirection'       => 'target',
                'typeId'                      => 'cmis:folder',
                'includeSubRelationshipTypes' => true,
            ),
            'includePolicyIds' => true,
        );

        $object = is_numeric($objectId)
                ? $objectService->getObject($objectId, $options)
                : $objectService->getObjectByPath($objectId, $options);
        return $object;

        /*$objectFolders = $relationshipService->getObjectRelationships($objectId, array(
            'relationshipDirection'       => 'target',
            'typeId'                      => 'cmis:folder',
            'includeSubRelationshipTypes' => true,
        ));
        $objectFoldersValues = array();
        foreach($objectFolders as $folder) {
            $objectFoldersValues[] = $folder['cmis:objectId'];
        }
        $object['folderId'] = $objectFoldersValues;
        */
        return $object;
    }

    public function deleteAction()
    {

    }
}
