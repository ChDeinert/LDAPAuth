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
 * This Class provides the Group API
 */
class LDAPAuth_Api_Group extends LDAPAuth_AbstractApi
{
    /**
     * Get all AD Groups, that are not in Zikula
     *
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getGroups2Import()
    {
        // Get LDAP Server Connection information
        $ldapConf = ModUtil::apiFunc($this->name, 'user', 'getServerConfig');
        $groups2Import = [];

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));
                $ldapGroups = $adldap->group()->all(true);

                foreach ($ldapGroups as $key => $val) {
                    $zk_gid = ModUtil::apiFunc('Groups', 'admin', 'getgidbyname', ['name' => $key]);

                    if (empty($zk_gid) || $zk_gid == false) {
                        $groups2Import[] = [
                            'name' => $key,
                            'desc' => $val
                        ];
                    }
                }

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
            }
        }

        return $groups2Import;
    }

    public function importGroup(array $args)
    {
        $this->validator->hasValues($args, ['group']);

        // Get LDAP Server Connection information
        $ldapConf = ModUtil::apiFunc($this->name, 'user', 'getServerConfig');

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));
                $groupinfo = $adldap->group()->infoCollection($args['group']);
                $obj = [
                    'name'          => $args['group'],
                    'gtype'         => 0,
                    'state'         => 0,
                    'nbumax'        => 0,
                    'description'   => isset($groupinfo->description) ? $groupinfo->description : $args['group']
                ];

                // Neue Gruppe anlegen
                $zk_gid = ModUtil::apiFunc('Groups', 'admin', 'create', $obj);
                $createEvent = new Zikula_Event('group.create', $obj);
                $this->eventManager->notify($createEvent);
                $this->addMembers($zk_gid, $adldap->group()->members($args['group']));

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
            }
        }
    }

    public function setUserMembership(array $args)
    {
        $this->validator->hasValues($args, ['uid', 'group']);

        $zk_gid = ModUtil::apiFunc('Groups', 'admin', 'getgidbyname', ['name' => $args['group']]);

        if (!empty($zk_gid) && $zk_gid !== false) {
            $object = [
                'gid' => $zk_gid,
                'uid' => $args['uid'],
            ];

            if (!ModUtil::apiFunc('Groups', 'user', 'isgroupmember', $object)) {
                DBUtil::insertObject($object, 'group_membership');
            }
        }
    }

    protected function addMembers($groupID, array $memberarray)
    {
        if (!isset($groupID) || empty($groupID)) {
            throw new Zikula_Exception_Fatal();
        }
        foreach ($memberarray as $key => $val) {
            $zk_uid = UserUtil::getIdFromName($val);
            $object = [
                'gid' => $groupID,
                'uid' => $zk_uid,
            ];

            if (!empty($zk_uid) &&
                $zk_uid !== false &&
                !ModUtil::apiFunc('Groups', 'user', 'isgroupmember', $object)
            ) {
                // Insert user into the Group
                $res = DBUtil::insertObject($object, 'group_membership');

                if ($res === false) {
                    LogUtil::registerError($this->__f('Warning! Could not add User %s to group!', [$val]));
                }
            }
        }
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
