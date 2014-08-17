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
 * This Class provides the Administrative API
 */
class LDAPAuth_Api_Admin extends LDAPAuth_AbstractApi
{
    /**
     * Returns an Array with the links to Administrative functions
     *
     * @return array
     */
    public function getLinks()
    {
        $links = [];

        if (SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_ADMIN)) {
            $links[] = [
                'url'   => ModUtil::url($this->name, 'admin', 'view'),
                'text'  => $this->__('View LDAPAuth Configuration'),
                'class' => 'z-icon-es-view'
            ];
            $links[] = [
                'url'   => ModUtil::url($this->name, 'admin', 'viewProfileMapping'),
                'text'  => $this->__('LDAP-Profile Mapping'),
                'class' => 'z-icon-es-config'
            ];
            $links[] = [
                'url'   => ModUtil::url($this->name, 'admin', 'userUpdate'),
                'text'  => $this->__('Update users'),
                'class' => 'z-icon-es-user'
            ];
            $links[] = [
                'url'   => ModUtil::url($this->name, 'admin', 'groupImport'),
                'text'  => $this->__('Group import'),
                'class' => 'z-icon-es-import'
            ];
            $links[] = [
                'url'   => ModUtil::url($this->name, 'admin', 'userImport'),
                'text'  => $this->__('User import'),
                'class' => 'z-icon-es-import'
            ];
        }

        return $links;
    }

    public function saveServerConfig(array $args)
    {
        // active
        $this->setVar('active', $args['active']);

        // Support for Profile-Module
        $this->setVar('profile', $args['profile']);

        // account_suffix
        $this->setVar('account_suffix', $args['account_suffix']);

        // base_dn
        $this->setVar('base_dn', $args['base_dn']);

        // domain_controllers
        $this->setVar('domain_controllers', $args['domain_controllers']);

        // admin_username
        $this->SetVar('admin_username', $args['admin_username']);

        // admin_password
        $this->setVar('admin_password', $args['admin_password']);

        // real_primarygroup
        $this->setVar('real_primarygroup', $args['real_primarygroup']);

        // use_ssl
        $this->setVar('use_ssl', $args['use_ssl']);

        // use_tsl
        $this->setVar('use_tsl', $args['use_tsl']);

        // recursive_groups
        $this->setVar('recursive_groups', $args['recursive_groups']);

        // ad_port
        $this->setVar('ad_port', $args['ad_port']);

        // sso
        $this->setVar('sso', $args['sso']);
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
