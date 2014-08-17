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
 * This Class provides the User/Standard API
 */
class LDAPAuth_Api_User extends LDAPAuth_AbstractApi
{
    /**
     * Returns the Configuration
     *
     * @return array
     */
    public function getServerConfig()
    {
        $domain_controllers = $this->getVar('domain_controllers');

        if (strpos($domain_controllers, ',') !== false) {
            $domain_controllers = explode(',', $domain_controllers);
        } else {
            $domain_controllers = [$domain_controllers];
        }

        $configuration = [
            'active'                => $this->getVar('active'),
            'profile'               => $this->getVar('profile'),
            'account_suffix'        => $this->getVar('account_suffix'),
            'base_dn'               => $this->getVar('base_dn'),
            'domain_controllers'    => $domain_controllers,
            'admin_username'        => $this->getVar('admin_username'),
            'admin_password'        => $this->getVar('admin_password'),
            'real_primarygroup'     => $this->getVar('real_primarygroup'),
            'use_ssl'               => $this->getVar('use_ssl'),
            'use_tsl'               => $this->getVar('use_tsl'),
            'recursive_groups'      => $this->getVar('recursive_groups'),
            'ad_port'               => $this->getVar('ad_port'),
            'sso'                   => $this->getVar('sso'),
        ];

        return $configuration;
    }

    /**
     * Returns informations about an user
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getUserinformation(array $args)
    {
        $this->validator->hasValues($args, ['uid']);

        $where = 'uid = '.$args['uid'];
        $items = DBUtil::selectExpandedObject('ldapauth_adinfo', [], $where);

        return $items;
    }

    /**
     * Checks whether the given user is avaiable in AD
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return boolean
     */
    public function isLDAP(array $args)
    {
        $this->validator->hasValues($args, ['uname']);

        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();
        $isLDAPUser = true;

        try {
            // Connect to LDAP-Server
            $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));
            $userdata = $adldap->user()->infoCollection($args['uname']);

            if (empty($userdata)) {
                $isLDAPUser = false;
            }

            // Close connection to LDAP-Server
            $adldap->close();
        } catch (\adLDAP\adLDAPException $e) {
            $isLDAPUser = false;
        }

        return $isLDAPUser;
    }

    /**
     * Checks whether the given user is active in AD
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return boolean
     */
    public function isActive(array $args)
    {
        $this->validator->hasValues($args, ['uname']);

        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();
        $isActive = true;

        try {
            // Connect to LDAP-Server
            $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));
            $userdata = $adldap->user()->info($args['uname'], ['useraccountcontrol']);
            $isActive = (($userdata[0]['useraccountcontrol'][0] & 2) == 0);

            // Close connection to LDAP-Server
            $adldap->close();
        } catch (\adLDAP\adLDAPException $e) {
            $isActive = true;
        }

        return $isActive;
    }

    /**
     * Deactivates an deactivated AD User in Zikula
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     */
    public function deactivate(array $args)
    {
        $this->validator->hasValues($args, ['uname']);

        $userObj = UserUtil::getVars(UserUtil::getIdFromName($args['uname']));
        $userObj['activated'] = 0;
        DBUtil::updateObject($userObj, 'users', '', 'uid');
    }

    /**
     * Get all AD users, whose last change in AD is newer than last update in Zikula
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getUsers2Update(array $args)
    {
        $items = [];
        $users = ModUtil::apiFunc('Users', 'user', 'getAll');

        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));

                foreach ($users as $user) {
                    $userdata = $adldap->user()->info($user['uname'], ['*']);
                    $adinfo = $this->getUserinformation(['uid' => $user['uid']]);

                    if ($userdata[0]['whenchanged'][0] != $adinfo['lastupdated']) {
                        $items[] = [
                            'uid'           => $user['uid'],
                            'uname'         => $user['uname'],
                            'lastchange'    => $adinfo['lastupdated'],
                            'whenchanged'   => $userdata[0]['whenchanged'][0],
                        ];
                    }
                }

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
            }
        }

        return $items;
    }

    /**
     * Updates information about an user
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     */
    public function updateInformation(array $args)
    {
        $this->validator->hasValues($args, ['uid']);

        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();
        $userObj = UserUtil::getVars($args['uid']);

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));

                // Infos zum User aus dem LDAP-Verzeichnis holen
                if ($user = $adldap->user()->infoCollection($userObj['uname'])) {
                    // Email aktualisieren
                    $userObj['email'] = mb_strtolower($user->mail);
                    $userObj['passreminder'] = 'LDAP';

                    if ($this->isActive(['uname' => $userObj['uname']])) {
                        $userObj['activated'] = 1;
                    } else {
                        $userObj['activated'] = 0;
                    }

                    if (isset($args['pass']) && !empty($args['pass'])) {
                        $userObj['pass'] = UserUtil::getHashedPassword($args['pass']);
                    } else {
                        unset($userObj['pass']);
                    }

                    $userdata = $adldap->user()->info($userObj['uname'], ['*']);
                    $adinfoObj = [
                        'uid'                       => $args['uid'],
                        'lastupdated'               => $userdata[0]['whenchanged'][0],
                        'cn'                        => $userdata[0]['cn'][0],
                        'sn'                        => $userdata[0]['sn'][0],
                        'telephonenumber'           => $userdata[0]['telephonenumber'][0],
                        'facsimiletelephonenumber'  => $userdata[0]['facsimiletelephonenumber'][0],
                        'givenname'                 => $userdata[0]['givenname'][0],
                        'distinguishedname'         => $userdata[0]['distinguishedname'][0],
                        'whencreated'               => $userdata[0]['whencreated'][0],
                        'whenchanged'               => $userdata[0]['whenchanged'][0],
                        'displayname'               => $userdata[0]['displayname'][0],
                        'department'                => $userdata[0]['department'][0],
                        'company'                   => $userdata[0]['company'][0],
                        'name'                      => $userdata[0]['name'][0],
                        'countrycode'               => $userdata[0]['countrycode'][0],
                        'samaccountname'            => $userdata[0]['samaccountname'][0],
                        'userprincipalname'         => $userdata[0]['userprincipalname'][0],
                        'objectcategory'            => $userdata[0]['objectcategory'][0],
                        'mail'                      => $userdata[0]['mail'][0],
                        'manager'                   => $userdata[0]['manager'][0],
                    ];
                    $adinfo = $this->getUserinformation([
                        'uid' => $args['uid'],
                    ]);

                    if (!isset($adinfo['uid'])) {
                        DBUtil::insertObject($adinfoObj, 'ldapauth_adinfo', 'uid', true);
                    } else {
                        DBUtil::updateObject($adinfoObj, 'ldapauth_adinfo', 'uid = '.$args['uid'], 'uid');
                        DBUtil::updateObject($userObj, 'users', 'uid = '.$args['uid'], 'uid');
                    }

                    if (ModUtil::available('Profile') && $ldapConf['profile'] == 1) {
                        $this->updateUserProfile([
                            'uid' => $args['uid'],
                            'uname' => $userObj['uname'],
                            'adldap' => $adldap,
                        ]);
                    }

                    $groups = $adldap->user()->groups($userObj['uname']);

                    foreach ($groups as $group) {
                        ModUtil::apiFunc($this->name, 'Group', 'setUserMembership', [
                            'uid'       => $args['uid'],
                            'group'     => $group,
                            'adldap'    => $adldap,
                        ]);
                    }
                }

                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
            }
        }
    }

    /**
     * Updates the User Profile
     *
     * @param array $args
     * @return boolean
     */
    public function updateUserProfile(array $args)
    {
        if (!isset($args['adldap']) || empty($args['adldap'])) {
            LogUtil::registerError($this->__('Warning! Profile data could not be updated!'));

            return false;
        }

        $ldapConf = $this->getServerConfig();

        if (ModUtil::available('Profile') && $ldapConf['profile'] == 1) {
            $adldap = $args['adldap'];
            $user = $adldap->user()->info($args['uname'], ['*']);
            $mappings = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAll');

            foreach ($mappings as $mapping) {
                $prop_obj = ModUtil::apiFunc('Profile', 'user', 'get', ['propid' => $mapping['prop_id']]);
                UserUtil::setVar($prop_obj['prop_attribute_name'], $user[0][$mapping['attribute']][0], $args['uid']);
            }
        }
    }

    /**
     * Get all AD users, that are not in Zikula
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getUsers2Import(array $args)
    {
        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();
        $users2Import = [];

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));
                $ldapUser = $adldap->user()->all(true);

                foreach ($ldapUser as $key => $val) {
                    $zk_uid = UserUtil::getIdFromName($key);
                    $userdata = $adldap->user()->info($key, ['useraccountcontrol', 'mail']);

                    if (empty($zk_uid) || $zk_uid == false) {
                        $users2Import[] = [
                            'name'      => $key,
                            'realname'  => $val,
                            'email'     => $userdata[0]['mail'][0],
                            'active'    => (($userdata[0]['useraccountcontrol'][0] & 2) == 0),
                        ];
                    }
                }

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
            }
        }

        return $users2Import;
    }

    /**
     * Processes the import of an user
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     */
    public function importUser(array $args)
    {
        $this->validator->hasValues($args, ['uname']);

        // Get LDAP Server Connection information
        $ldapConf = $this->getServerConfig();

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP($this->getLDAPConnectionArray($ldapConf));

                $userinfo = $adldap->user()->infoCollection($args['uname']);
                $userdata = $adldap->user()->info($args['uname'], ['useraccountcontrol']);

                if (($userdata[0]['useraccountcontrol'][0] & 2) == 0) {
                    $active = Users_Constant::ACTIVATED_ACTIVE;
                } else {
                    $active = Users_Constant::ACTIVATED_INACTIVE;
                }

                // Get Userdata from Ldap-Server
                $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
                $nowUTCStr = $nowUTC->format(Users_Constant::DATETIME_FORMAT);
                $uid = UserUtil::getIdFromName($args['uname'], true);
                $userObj = [];
                $userObj['uid'] = $uid;
                $userObj['uname'] = mb_strtolower($args['uname']);
                $userObj['email'] = mb_strtolower($userinfo->mail);
                $userObj['pass'] = UserUtil::getHashedPassword(hash('sha-256', uniqid(serialize($_SERVER), true)));
                $userObj['passreminder'] = 'LDAP';
                $userObj['activated'] = $active;
                $userObj['approved_date'] = $nowUTCStr;
                $userObj['approved_by'] = 2;
                $userObj['regdate'] = $nowUTCStr;
                $userObj['lastlogin'] = $nowUTCStr;
                $userObj['theme'] = '';
                $userObj['ublockon'] = 0;
                $userObj['ublock'] = '';
                $userObj['tz'] = '';
                $userObj['locale'] = '';
                $userObj = DBUtil::insertObject($userObj, 'users', 'uid');

                if ($userObj) {
                    $userUpdateObj = [
                        'uid'           => $userObj['uid'],
                        'approved_by'   => $userObj['uid'],
                    ];

                    // Use DBUtil so we don't get an update event. The create hasn't happened yet.
                    DBUtil::updateObject($userUpdateObj, 'users', '', 'uid');

                    // Add user to default group
                    $defaultGroup = ModUtil::getVar('Groups', 'defaultgroup', false);

                    if (!$defaultGroup) {
                        $this->registerError($this->__('Warning! The user account was created, but there was a problem adding the account to the default group.'));
                    }

                    $groupAdded = ModUtil::apiFunc('Groups', 'user', 'adduser', [
                        'gid' => $defaultGroup,
                        'uid' => $userObj['uid']
                    ]);

                    if (!$groupAdded) {
                        $this->registerError($this->__('Warning! The user account was created, but there was a problem adding the account to the default group.'));
                    }

                    // Force the reload of the user in the cache.
                    $userObj = UserUtil::getVars($userObj['uid'], true);

                    // ATTENTION: This is the proper place for the item-create hook, not when a pending
                    // registration is created. It is not a "real" record until now, so it wasn't really
                    // "created" until now. It is way down here so that the activated state can be properly
                    // saved before the hook is fired.
                    $createEvent = new Zikula_Event('user.account.create', $userObj);
                    $this->updateInformation([
                        'uid'       => $userObj['uid'],
                        'pass'      => UserUtil::getHashedPassword(hash('sha-256', uniqid(serialize($_SERVER), true))),
                        'adldap'    => $adldap,
                    ]);
                }

                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                LogUtil::registerError($this->__('Warning! Connection to AD could not be established!'));
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
