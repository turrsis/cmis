<?php
$adapter = $sql->getAdapter();

$objectsTables = $adapter->query(
    $sql->select('cmis:meta_types')->columns(['localName']),
    'execute'
);

$objectsTables = array_merge(
    array_column($objectsTables->toArray(), 'localName'),
    [
        'cmis:meta_types',
        'cmis:meta_properties',
        'cmis:meta_types_properties',
        'cmis_object',
        'cmis:repository_info'
    ]
);
foreach($objectsTables as $table) {
    $adapter->query(
        $sql->getDdl()->dropTable($table)->ifExists(true),
        'execute'
    );
}

return true;