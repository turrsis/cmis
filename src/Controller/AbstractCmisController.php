<?php
namespace Tursis\Cmis\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractCmisController extends AbstractActionController
{
    protected $cmis = null;

    protected $formElementManager = null;

    /**
     * @return \Cmis\Cmis\Repository
     */
    protected function getRepository($name = 'default')
    {
        return $this->getServiceLocator()->get('cmis:repo:' . $name);
    }

    protected function getReferencesParams($references)
    {
        return array_replace($references, $this->params('references', array()));
    }

    protected function getFormElement($name, $params = array())
    {
        if (!$this->formElementManager) {
            $this->formElementManager = $this->getServiceLocator()->get('FormElementManager');
        }
        return $this->formElementManager->get($name, $params);
    }

    protected function resolveObjectIdParam($objectIdParam, $prefixFolder)
    {
        $objectId = $this->params($objectIdParam);
        if (stripos($objectId, '/') !== false) {
            $objectId = rtrim($prefixFolder, '/') . '/' . $objectId;
        }
        return $objectId;
    }
}
