<?php
namespace Cmis\Controller;

use Zend\Stdlib\ArrayTreeIterator;

class FolderController extends AbstractCmisController
{
    public function indexAction()
    {
        $cmisRepository = $this->getRepository();
        $parentFolder = $this->params('folder');
        if ($parentFolder === null) {
            $parentFolder = $cmisRepository->getRepositoryService()->getRepositoryInfo()->rootFolderId();
        }
        
        $parentFolder = $cmisRepository->getObjectService()->getObject($parentFolder);
        
        $parentFolderId = $parentFolder['properties']['cmis:objectId'];
        $opts = array('depth'=>-1);

        return array(
            'parent'     => $parentFolder,
            'folders'    => new ArrayTreeIterator(
                $cmisRepository->getNavigationService()->getFolderTree($parentFolderId, $opts),
                array('childsField' => 'childs')
            ),
            'references' => $this->getReferencesParams(array(
                'create'    => ["params"=>["containers"=>["content"=>["controller"=>"cmis-object","action"=>"create",  "folderId"=>$parentFolderId]]]],
                'edit'      => ['params'=>["containers"=>["content"=>["controller"=>"cmis-object","action"=>"edit",    "object"  =>null]]]],
                'documents' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-folder","action"=>"children","folder"  =>null]]]],
            )),
        );
    }

    public function childrenAction()
    {
        $folder = $this->params('folder');
        if (stripos($folder, '/') !== false) {
            $folder = $this->getCmis()->objectService()->getObjectByPath(null, $folder);
            $folder = $folder['properties']['cmis:objectId'];
        }

        return array(
            'children'   => $this->getCmis()->navigationService()->getChildren(null, $folder),
            'folderId'   => $folder,
            'references' => $this->getReferencesParams(array(
                'create' => ["params"=>["containers"=>["content"=>["controller"=>"cmis-object","action"=>"create","folderId"=>$folder]]]],
                'edit'   => ['params'=>["containers"=>["content"=>["controller"=>"cmis-object","action"=>"edit",  "object"=>null]]]],
            )),
        );
    }
}
