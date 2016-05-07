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
 * The user authentication services for the log-in process through the core Users table and aditional LDAP-Auth Table.
 */
class LDAPAuth_Api_Authentication extends Zikula_Api_AbstractAuthentication
{
    protected $authenticationMethods = array();

    /**
     * Post initialise: called from constructor
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function  postInitialize()
    {
        parent::postInitialize();

        // Get LDAP Server Connection information
        $ldapConf = ModUtil::apiFunc($this->name, 'user', 'getServerConfig');

        if ($ldapConf['active'] == 1) {
            // Register the uname authentication method
            $authenticationMethod = new Users_Helper_AuthenticationMethod(
                $this->name,
                'ldap',
                $this->__('User name'),
                $this->__('User name and password'),
                true
            );
            $authenticationMethod->enableForAuthentication();
            $authenticationMethod->enableForRegistration();
            $this->authenticationMethods['ldap'] = $authenticationMethod;
        }
    }

    /**
     * Informs the calling function whether this authentication module is reentrant or not
     *
     * @see Zikula_Api_AbstractAuthentication::isReentrant()
     */
    public function isReentrant()
    {
        return true;
    }

    /**
     * Indicate whether this module supports the indicated authentication method
     *
     * @see Zikula_Api_AbstractAuthentication::supportsAuthenticationMethod()
     */
    public function supportsAuthenticationMethod(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            $methodName = $args['method'];
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        $isSupported = (bool)isset($this->authenticationMethods[$methodName]);

        return $isSupported;
    }

    /**
     * Indicates whether a specified authentication method that is supported by this module is enabled for use
     *
     * @see Zikula_Api_AbstractAuthentication::isEnabledForAuthentication()
     */
    public function isEnabledForAuthentication(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            if (isset($this->authenticationMethods[$args['method']])) {
                $authenticationMethod = $this->authenticationMethods[$args['method']];
            } else {
                throw new Zikula_Exception_Fatal(
                    $this->__f(
                        'An unknown method (\'%1$s\') was received.',
                        [$args['method']]
                    )
                );
            }
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        return $authenticationMethod->isEnabledForAuthentication();
    }

    /**
     * Retrieves an array of authentication methods defined by this module, possibly filtered by only those that are enabled
     *
     * @see Zikula_Api_AbstractAuthentication::getAuthenticationMethods()
     */
    public function getAuthenticationMethods(array $args = null)
    {
        if (isset($args) && isset($args['filter'])) {
            if (is_numeric($args['filter']) && ((int) $args['filter'] == $args['filter'])) {
                switch ($args['filter']) {
                    case Zikula_Api_AbstractAuthentication::FILTER_NONE:
                    case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                    case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
                        $filter = $args['filter'];
                        break;
                    default:
                        throw new Zikula_Exception_Fatal(
                            $this->__f(
                                'An unknown value for the \'filter\' parameter was received (\'%1$d\').',
                                [$args['filter']]
                            )
                        );
                        break;
                }
            } else {
                throw new Zikula_Exception_Fatal(
                    $this->__f(
                        'An invalid value for the \'filter\' parameter was received (\'%1$s\').',
                        [$args['filter']]
                    )
                );
            }
        } else {
            $filter = Zikula_Api_AbstractAuthentication::FILTER_NONE;
        }

        switch ($filter) {
            case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $index => $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForAuthentication()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] -> $authenticationMethod;
                    }
                }
                break;
            case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $index => $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForRegistration()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] -> $authenticationMethod;
                    }
                }
                break;
            default:
                $authenticationMethods = $this->authenticationMethods;
                break;
        }

        return $authenticationMethods;
    }

    /**
     * Retrieves an authentication method defined by this module.
     *
     * Parameters passed in $args:
     * ---------------------------
     * string 'method' The name of the authentication method.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return array An array containing the authentication method requested.
     *
     * @throws \InvalidArgumentException Thrown if invalid parameters are sent in $args.
     */
    public function getAuthenticationMethod(array $args)
    {
        if (!isset($args['method'])) {
            throw new \InvalidArgumentException($this->__f('An invalid value for the \'method\' parameter was received (\'%1$s\').', array($args['method'])));
        }

        if (!isset($this->authenticationMethods[($args['method'])])) {
            throw new \InvalidArgumentException($this->__f('The requested authentication method \'%1$s\' does not exist.', array($args['method'])));
        }

        return $this->authenticationMethods[($args['method'])];
    }

    /**
     * Registers a user account record or a user registration request with the authentication method
     *
     * @see Zikula_Api_AbstractAuthentication::register()
     */
    public function register(array $args)
    {
        $authenticationInfo = $args['authentication_info'];

        // Get LDAP Server Connection information
        $ldapConf = ModUtil::apiFunc($this->name, 'user', 'getServerConfig');

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP([
                    'account_suffix'        => $ldapConf['account_suffix'],
                    'base_dn'               => $ldapConf['base_dn'],
                    'domain_controllers'    => $ldapConf['domain_controllers'],
                    'admin_username'        => $ldapConf['admin_username'],
                    'admin_password'        => $ldapConf['admin_password'],
                    'real_primarygroup'     => $ldapConf['real_primarygroup'],
                    'use_ssl'               => $ldapConf['use_ssl'],
                    'use_tsl'               => $ldapConf['use_tsl'],
                    'recursive_groups'      => $ldapConf['recoursive_groups'],
                    'ad_port'               => $ldapConf['ad_port'],
                    'sso'                   => $ldapConf['sso']
                ]);

                if ($user = $adldap->user()->infoCollection($authenticationInfo['login_id'])) {
                    // Get Userdata from Ldap-Server
                    $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
                    $nowUTCStr = $nowUTC->format(Users_Constant::DATETIME_FORMAT);
                    $userObj = array();
                    $userObj['uid'] = $authenticationInfo['uid'];
                    $userObj['uname'] = mb_strtolower($authenticationInfo['login_id']);
                    $userObj['email'] = mb_strtolower($user->mail);
                    $userObj['pass'] = UserUtil::getHashedPassword($authenticationInfo['pass']);
                    $userObj['passreminder'] = 'LDAP';
                    $userObj['activated'] = Users_Constant::ACTIVATED_ACTIVE;
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
                    }

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

                    // Close connection to LDAP-Server
                    $adldap->close();
                    return $userObj;
                }

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                throw new Zikula_Exception_Fatal();
            }
        }

        return false;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning a simple boolean result
     *
     * @see Zikula_Api_AbstractAuthentication::checkPassword()
     */
    public function checkPassword(array $args)
    {
        if (!isset($args['authentication_info']) ||
            !is_array($args['authentication_info']) ||
            empty($args['authentication_info'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalid \'%1$s\' parameter received in a call to %2$s',
                    ['authentication_info', __METHOD__]
                )
            );
        }
        if (!isset($args['authentication_method']) ||
            !is_array($args['authentication_method']) ||
            empty($args['authentication_method'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalid \'%1$s\' parameter received in a call to %2$s',
                    ['authentication_method', __METHOD__]
                )
            );
        }

        $authenticationInfo = $args['authentication_info'];
        $authenticationMethod = $args['authentication_method'];
        $passwordAuthenticates = false;

        // Get LDAP Server Connection information
        $ldapConf = ModUtil::apiFunc($this->name, 'user', 'getServerConfig');

        if ($ldapConf['active'] == 1) {
            try {
                // Connect to LDAP-Server
                $adldap = new \adLDAP\adLDAP([
                    'account_suffix'        => $ldapConf['account_suffix'],
                    'base_dn'               => $ldapConf['base_dn'],
                    'domain_controllers'    => $ldapConf['domain_controllers'],
                    'admin_username'        => $ldapConf['admin_username'],
                    'admin_password'        => $ldapConf['admin_password'],
                    'real_primarygroup'     => $ldapConf['real_primarygroup'],
                    'use_ssl'               => $ldapConf['use_ssl'],
                    'use_tsl'               => $ldapConf['use_tsl'],
                    'recursive_groups'      => $ldapConf['recoursive_groups'],
                    'ad_port'               => $ldapConf['ad_port'],
                    'sso'                   => $ldapConf['sso']
                ]);
                $isLdapUser = ModUtil::apiFunc($this->name, 'User', 'isLDAP', ['uname' => $authenticationInfo['login_id']]);

                if ($isLdapUser) {
                    $isActiveUser = ModUtil::apiFunc($this->name, 'User', 'isActive', ['uname' => $authenticationInfo['login_id']]);
                } else {
                    $isActiveUser = false;
                }
                if ($isLdapUser && $isActiveUser) {
                    if ($adldap->authenticate($authenticationInfo['login_id'], $authenticationInfo['pass'])) {
                        $passwordAuthenticates = true;
                    } else {
                        $passwordAuthenticates = ModUtil::apiFunc(
                            'Users',
                            'Authentication',
                            'checkPassword',
                            $args,
                            'Zikula_Api_AbstractAuthentication'
                        );
                    }
                } elseif ($isLdapUser && !$isActiveUser) {
                    // User is not an active LDAP-User. He should be deactivated in Zikula, too.
                    ModUtil::apiFunc($this->name, 'User', 'deactivate', ['uname' => $authenticationInfo]);
                    $adldap->close();

                    return false;
                } elseif (!$isLdapUser) {
                    $passwordAuthenticates = ModUtil::apiFunc(
                        'Users',
                        'Authentication',
                        'checkPassword',
                        $args,
                        'Zikula_Api_AbstractAuthentication'
                    );
                }

                // Close connection to LDAP-Server
                $adldap->close();
            } catch (\adLDAP\adLDAPException $e) {
                $passwordAuthenticates = ModUtil::apiFunc(
                    'Users',
                    'Authentication',
                    'checkPassword',
                    $args,
                    'Zikula_Api_AbstractAuthentication'
                );
            }
        } else {
            // try Strandard-Auth
            $passwordAuthenticates = ModUtil::apiFunc(
                'Users',
                'Authentication',
                'checkPassword',
                $args,
                'Zikula_Api_AbstractAuthentication'
            );
        }
        if (!$passwordAuthenticates && !$this->request->getSession()->hasMessages(Zikula_Session::MESSAGE_ERROR)) {
            if ($authenticationMethod['method'] == 'email') {
                $this->registerError($this->__('Sorry! The e-mail address or password you entered was incorrect.'));
            } else {
                $this->registerError($this->__('Sorry! The user name or password you entered was incorrect.'));
            }
        }

        return $passwordAuthenticates;
    }

    /**
     * Returns a "clean" version of the authenticationInfo used to log in, without any password-like information
     *
     * @param array $args
     * @throws Zikula_Exception_Fatal
     * @return array
     */
    public function getAuthenticationInfoForSession(array $args)
    {
        // Validate authenticationInfo
        if (!isset($args['authentication_info']) ||
            !is_array($args['authentication_info']) ||
            empty($args['authentication_info'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalied \'%1$s\' parameter received in a call to %2$s',
                    ['authentication_info', __METHOD__]
                )
            );
        }

        $fieldsToClean = [
            'pass',
            'new_pass',
            'confirm_new_pass',
            'pass_reminder',
        ];

        foreach ($fieldsToClean as $fieldName) {
            if (array_key_exists($fieldName, $authenticationInfo)) {
                unset($authenticationInfo[$fieldName]);
            }
        }

        return $authenticationInfo;
    }

    /**
     * Retrieves the Zikula User ID (uid) for the given authentication info from the mapping maintained by this authentication module
     *
     * @see Zikula_Api_AbstractAuthentication::getUidForAuthenticationInfo()
     */
    public function getUidForAuthenticationInfo(array $args)
    {
        $authenticatedUid = false;

        // Validate authenticationInfo
        if (!isset($args['authentication_info']) ||
            !is_array($args['authentication_info']) ||
            empty($args['authentication_info'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalid \'%1$s\' parameter provided in a call to %2$s.',
                    ['authentication_info', __METHOD__]
                )
            );
        }

        $authenticationInfo = $args['authentication_info'];

        if (!isset($args['authentication_method']) ||
            !is_array($args['authentication_method']) ||
            empty($args['authentication_method'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalid \'%1$s\' parameter provided in a call to %2$s.',
                    ['authentication_method', __METHOD__]
                )
            );
        }

        $authenticationMethod = $args['authentication_method'];
        $loginID = $authenticationInfo['login_id'];

        if (!isset($loginID) || (is_string($loginID) && empty($loginID))) {
            $detailedMessage = $this->__f('A user name was not provided in a call to %1$s.', [__METHOD__]);
            throw new Zikula_Exception_Fatal($detailedMessage);
        } elseif (!is_string($loginID)) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Invalid type for \'%1$s\' parameter in a call to %2$s.',
                    ['login_id', __METHOD__]
                )
            );
        }

        $loginID = mb_strtolower($loginID);
        $authenticatedUid = UserUtil::getIdFromName($loginID);

        if (!$authenticatedUid) {
            // Might be a registration. See above.
            $authenticatedUid = UserUtil::getIdFromName($loginID, true);
        }

        return $authenticatedUid;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning the matching Zikula user id
     *
     * @see Zikula_Api_AbstractAuthentication::authenticateUser()
     */
    public function authenticateUser(array $args)
    {
        $authenticatedUid = false;
        $checkPassword = $this->checkPassword($args);

        if ($checkPassword) {
            $authenticatedUid = $this->getUidForAuthenticationInfo($args);

            if (!$authenticatedUid) {
                // Hier muss wohl der neue User erstellt werden
                $userObj = $this->register($args);
                $authenticatedUid = $userObj['uid'];

                if (empty($authenticatedUid)) {
                    // Falls das dann nicht klappt, muss die Fehlermeldung kommen
                    $this->registerError($this->__('Sorry! The user name or password you entered was incorrect.'));
                }
            }
        }

        ModUtil::apiFunc($this->name, 'User', 'updateInformation', [
            'uid'  => $authenticatedUid,
            'pass' => $args['authentication_info']['pass'],
        ]);

        return $authenticatedUid;
    }

    /**
     * Retrieve the account recovery information for the specified user
     *
     * @see Zikula_Api_AbstractAuthentication::getAccountRecoveryInfoForUid()
     */
    public function getAccountRecoveryInfoForUid(array $args)
    {
        if (!isset($args) || empty($args)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid parameter array was received.'));
        }

        $uid = isset($args['uid']) ? $args['uid'] : false;

        if (!isset($uid) || !is_numeric($uid) || ((string) ((int) $uid) != $uid)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid user id was received.'));
        }

        $userObj = UserUtil::getVars($uid);
        $lostUserNames = [];

        if ($userObj) {
            if (!empty($userObj['pass']) && ($userObj['pass'] != Users_Constant::PWD_NO_USERS_AUTHENTICATION)) {
                $loginOption = $this->getVar(Users_Constant::MODVAR_LOGIN_METHOD, Users_Constant::DEFAULT_LOGIN_METHOD);

                if (($loginOption == Users_Constant::LOGIN_METHOD_UNAME) || ($loginOption == Users_Constant::LOGIN_METHOD_ANY)) {
                    $lostUserNames[] = [
                        'modname'           => $this->name,
                        'short_description' => $this->__('User name'),
                        'long_description'  => $this->__('User name'),
                        'uname'             => $userObj['uname'],
                        'link'              => '',
                    ];
                }

                if (($loginOption == Users_Constant::LOGIN_METHOD_EMAIL) || ($loginOption == Users_Constant::LOGIN_METHOD_ANY)) {
                    $lostUserNames[] = [
                        'modname'           => $this->name,
                        'short_description' => $this->__('E-mail Address'),
                        'long_description'  => $this->__('E-mail Address'),
                        'uname'             => $userObj['email'],
                        'link'              => '',
                    ];
                }
            }
        }

        return $lostUserNames;
    }
}
