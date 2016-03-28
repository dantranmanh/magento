<?php

$OMNIVORE_ROLE = 'omnivore_role';
$OMNIVORE_USER = 'omnivore_user';

$installer = $this;
$admin = Mage::getSingleton('admin/session')->getUser();
$adminUserId = null;
$adminEmail = null;

if (isset($admin))
{
    error_log("Admin user id = {$admin->getUserId()}, running installer = " . get_class($installer));
}
else
{
    error_log("Running installer " . get_class($installer) . ", admin user is not set!");
}

$installer->startSetup();

$tableName = $installer->getTable('citybeach_omnivore/rego');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true)
{
    error_log("Creating table {$tableName}");
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('rego_id', Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ),
            'Rego Id'
        )
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'User Id'
        )
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
            array(),
            'Email'
        )
        ->addColumn('key', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
            array(),
            'Key'
        )
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255,
            array(),
            'Key'
        );

    $installer->getConnection()->createTable($table);
}
else
{
    error_log("Table {$tableName} exists!");
}

// generate random key to be used as API key and Omnivore account password:
$key = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 20);
//error_log("Random key = {$key}");

// check if there are existing records in the rego table
$regos = Mage::getModel('citybeach_omnivore/rego')->getCollection();
if ($regos->count() > 0)
{
    error_log("Table {$tableName} contains {$regos->count()} records!");
}
else
{
    $rego = Mage::getModel('citybeach_omnivore/rego');

    $rego->setUserId($adminUserId);
    $rego->setEmail($adminEmail);
    $rego->setKey($key);
    $rego->setStatus('installed');

    $rego->save();
    error_log("Rego record created, rego id = {$rego->getId()}");
}

$role = Mage::getModel('api/roles');
$user = Mage::getModel('api/user');

// create the omnivore role if it doesn't exist
$roles = Mage::getModel('api/roles')->getCollection();
$roles->addFieldToFilter('role_name', $OMNIVORE_ROLE);
if ($roles->count() > 0)
{
    error_log("The API role {$OMNIVORE_ROLE} already exists, rules not updated!");
    $role = $roles->getFirstItem();
}
else
{
    $role = $role
        ->setName('omnivore_role')
        ->setPid(0)
        ->setRoleType('G')
        ->save();

    error_log("API role created, role id = {$role->getId()}");

    Mage::getModel("api/rules")
        ->setRoleId($role->getId())
        ->setResources(array("all"))
        ->saveRel();

    error_log("API rules created, permissions set to 'all' ");
}

// create the omnivore user if it doesn't exist
$users = Mage::getModel('api/user')->getCollection();
$users->addFieldToFilter('username', $OMNIVORE_USER);
if ($users->count() > 0)
{
    error_log("The API user {$OMNIVORE_USER} already exists, roles not updated!");
    $user = $users->getFirstItem();
}
else
{
    $user->setData(array(
        'username' => $OMNIVORE_USER,
        'firstname' => 'Omnivore',
        'lastname' => 'Api User',
        'email' => 'omnivore@citybeachsoftware.com',
        'api_key' => $key,
        'is_active' => 1,
        'user_roles' => '',
        'assigned_user_role' => '',
        'role_name' => '',
        'roles' => array($role->getId())
    ));
    $user->save();

    error_log("API user created, user id = " . $user->getId());

    $user->setRoleIds(array($role->getId()))
        ->setRoleUserId($user->getUserId())
        ->saveRelations();
}

$installer->endSetup();

// this does a clean uninstall:
//drop table citybeach_omnivore_rego;
//delete from core_resource where code = 'citybeach_omnivore_setup';
//delete from api_rule where role_id in (select role_id from api_role where role_name = 'omnivore_role');
//delete from api_role where role_type = 'U' and role_name = 'Omnivore';
//delete from api_role where role_type = 'G' and role_name = 'omnivore_role';
//delete from api_user where username = 'omnivore_user';
