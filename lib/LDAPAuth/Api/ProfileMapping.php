<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 * @subpackage Api
 */

/**
 * This Class provides the API for the Profile mappings
 */
class LDAPAuth_Api_ProfileMapping extends LDAPAuth_AbstractApi
{
    /**
     * Returns all Profile mappings
     *
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getAll()
    {
        $mappings = DBUtil::selectObjectArray('ldapauth_profile_mapping', '', 'id asc');

        if ($mappings === false) {
            throw new Zikula_Exception_Fatal();
        }
        foreach ($mappings as $key => $val) {
            $prop_obj = ModUtil::apiFunc('Profile', 'user', 'get', ['propid' => $val['prop_id']]);
            $mappings[$key]['prop_attribute_name'] = $prop_obj['prop_attribute_name'];
        }

        return $mappings;
    }

    /**
     * Returns a Profile mapping by ID
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function get(array $args)
    {
        $this->validator->hasValues($args, ['id']);

        $mapping = DBUtil::selectObjectByID('ldapauth_profile_mapping', $args['id']);

        if ($mapping === false) {
            throw new Zikula_Exception_Fatal();
        }

        return $mapping;
    }

    /**
     * Writes a Profile mappings data into DB
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function save(array $args)
    {
        $this->validator->hasValues($args, ['action']);

        $res = false;
        $obj = [
            'active'    => $args['active'],
            'prop_id'   => $args['prop_id'],
            'attribute' => $args['attribute'],
        ];

        if ($args['action'] == 'new') {
            $res = DBUtil::insertObject($obj, 'ldapauth_profile_mapping', 'id', true);
        } elseif ($args['action'] == 'edit') {
            if (!isset($args['id']) || $args['id'] == null) {
                throw new Zikula_Exception_Fatal();
            }

            $obj['id'] = $args['id'];
            $res = DBUtil::updateObject($obj, 'ldapauth_profile_mapping', ' id = '.$obj['id'], 'id');
        } else {
            throw new Zikula_Exception_Fatal();
        }
        if ($res !== false) {
            LogUtil::registerStatus($this->__('Profile mapping saved!'));
        } else {
            LogUtil::registerError($this->__('Error! Profile mapping could not be saved!'));
        }

        return $res;
    }

    /**
     * Removes a Profile mapping from the DB
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return boolean
     */
    public function delete(array $args)
    {
        $this->validator->hasValues($args, ['id']);

        $res = DBUtil::deleteObjectByID('ldapauth_profile_mapping', $args['id'], 'id');

        if ($res !== false) {
            LogUtil::registerStatus($this->__('Profile mapping was deleted!'));

            return true;
        } else {
            LogUtil::registerError($this->__('Error! Profile mapping could not be removed!'));

            return false;
        }
    }

    /**
     * Returns a list of supported AD-Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [
            'cn'                        => $this->__('CN'),
            'sn'                        => $this->__('SN'),
            'telephonenumber'           => $this->__('Telephonenumber'),
            'facsimiletelephonenumber'  => $this->__('Facsimiletelephonenumber'),
            'givenname'                 => $this->__('Givenname'),
            'distinguishedname'         => $this->__('Distinguishedname'),
            'whencreated'               => $this->__('Createdate'),
            'whenchanged'               => $this->__('Changedate'),
            'displayname'               => $this->__('Displayname'),
            'department'                => $this->__('Department'),
            'company'                   => $this->__('Company'),
            'name'                      => $this->__('Name'),
            'countrycode'               => $this->__('Countrycode'),
            'samaccountname'            => $this->__('SAMAccountname'),
            'userprincipalname'         => $this->__('Userprincipalname'),
            'objectcategory'            => $this->__('Objectcategory'),
            'mail'                      => $this->__('Mail'),
            'manager'                   => $this->__('Manager'),
        ];

        return $attributes;
    }

    /**
     * Returns a list of configured Profile Properties
     *
     * @return array
     */
    public function getProperties()
    {
        $items = [];

        if (ModUtil::available('Profile')) {
            $allprops = ModUtil::apiFunc('Profile', 'user', 'getall', [
                'startnum' => 0,
                'numitems' => -1,
            ]);

            foreach ($allprops as $prop) {
                $items[] = [
                    'prop_id'               => $prop['prop_id'],
                    'prop_label'            => $prop['prop_label'],
                    'prop_attribute_name'   => $prop['prop_attribute_name'],
                ];
            }
        }

        return $items;
    }

    /**
     * Post initialise: called from constructor
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function postInitialize()
    {
        parent::postInitialize();

        $this->validator = new LDAPAuth_Validator_ApiVal();
    }
}
