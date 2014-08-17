<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 */

/**
 * Abstract API for LDAPAuth module.
 */
abstract class LDAPAuth_AbstractApi extends Zikula_AbstractApi
{
    /**
     * Container for Controller validator instance
     */
    protected $validator;

    /**
     * Returns the Array to use for establishing the LDAP Connection
     *
     * It's for making sure, that only the correct keys are passed to ADLDAP
     *
     * @param array $ldapConf
     * @return array
     */
    protected function getLDAPConnectionArray(array $ldapConf)
    {
        $connArray = [
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
        ];

        return $connArray;
    }
}
