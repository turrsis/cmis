<?php
namespace Tursis\Cmis\Controller;

use Tursis\Cmis\Types\Properties;
use Zend\Stdlib\ArrayTreeIterator;

class TypeController extends AbstractCmisController
{
    public function indexAction()
    {
        return array(
            'types'      => new ArrayTreeIterator(
                $this->getRepository()->getRepositoryService()->getTypeDescendants(null),
                array('childsField' => 'childs')
            ),
            'references' => $this->getReferencesParams(array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-type","action"=>"create"]]]],
            )),
        );
    }

    public function editAction()
    {
        $repositoryService = $this->getRepository()->getRepositoryService();

        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form     = $formElementManager->get('cmis-edit-type', array(
            'name' => 'frmEditType'
        ));
        $type = $repositoryService
                        ->getTypeDefinition(null, $this->params('type'))
                        ->toArray(Properties::OWN_PROPERTIES);
        $parentType = $repositoryService
                        ->getTypeDefinition(null, $type['parentId'])
                        ->toArray(Properties::OWN_PROPERTIES);
        if (!$form->isPost()) {
            
            /*$type = array(
                'attributes' => $type,
                'propertyDefinitions' => $type['propertyDefinitions'],
            );
            unset($type['attributes']['propertyDefinitions']);*/
            $form->setData($type);
        } else {
            $post = $form->getPost();
            $form->setData($post);
            if ($form->isPost('addProperty')) {
                $properties = $form->get('propertyDefinitions');
                $properties->addRow();
            } elseif ($form->isPost('save') && $form->isValid()) {
                $type = $form->getData();
                $typeId    = $repositoryService->updateType(null, $type);
                return $this->redirect()->refresh();
            }
        }
        $form->prepare();
        return array(
            'form' => $form,
            'parentType' => $parentType,
        );
    }

    public function createAction()
    {
        $repositoryService = $this->getRepository()->getRepositoryService();

        $form     = $this->getServiceLocator()->get('FormElementManager')->get('cmis-create-type', array(
            'name' => 'frmCreateType'
        ));
        if ($form->isPost()) {
            $post = $form->getPost();
            $form->setData($post);
            if($form->isPost('addProperty')) {
                $properties = $form->get('propertyDefinitions');
                $properties->addRow();
            } elseif ($form->isPost('save') && $form->isValid()) {
                $data = $form->getData();
                $type    = $repositoryService->createType(null, $data);
                return $this->redirect()->toRoute(null, ['containers' => ['content'=>['controller'=>'cmis-type','action'=>'edit','type'=>$type['queryName']]]]);
            }

        }
        $form->prepare();
        return array(
            'form' => $form
        );
    }

    public function deleteAction()
    {
        return $this->redirect()->toReferer();
    }

    public function editPropertyAction()
    {

    }

    public function createPropertyAction()
    {

    }

    public function deletePropertyAction()
    {

    }
}
