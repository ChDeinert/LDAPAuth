<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 */

/**
 * LDAPAuth module installer.
 */
class LDAPAuth_Installer extends Zikula_AbstractInstaller
{
    /**
     * Install the Translator module.
     *
     * @see Zikula_AbstractInstaller::install()
     * @return boolean True on success or false on failure.
     */
    public function install()
    {
        // active
        $this->setVar('active', 0);

        // Support for Profile-Module
        $this->setVar('profile', 0);

        // account_suffix
        $this->setVar('account_suffix', '@mydomain.local');

        // base_dn
        $this->setVar('base_dn', 'DC=mydomain,DC=local');

        // domain_controllers
        $this->setVar('domain_controllers', 'dc01.mydomain.local');

        // admin_username
        $this->SetVar('admin_username', NULL);

        // admin_password
        $this->setVar('admin_password', NULL);

        // real_primarygroup
        $this->setVar('real_primarygroup', 1);

        // use_ssl
        $this->setVar('use_ssl', 0);

        // use_tsl
        $this->setVar('use_tsl', 0);

        // recursive_groups
        $this->setVar('recursive_groups', 1);

        // ad_port
        $this->setVar('ad_port', 389);

        // sso
        $this->setVar('sso', 0);

        // Profile-Mapping Table
        if (!DBUtil::createTable('ldapauth_profile_mapping')) {
            return false;
        }

        // ADInfo Table
        if (!DBUtil::createTable('ldapauth_adinfo')) {
            return false;
        }

        // Initialisation successful
        return true;
    }

    /**
     * Upgrade the Translator module from an old version.
     *
     * @see Zikula_AbstractInstaller::upgrade()
     * @param string $oldversion The version from which the upgrade is beginning (the currently installed version);
     * @return boolean True on success or false on failure.
     */
    public function upgrade($oldVersion)
    {
        /*switch ($oldVersion) {
            case '1.0.0':
        }*/

        // Update successful
        return true;
    }

    /**
     * Delete the Translator module.
     *
     * @see Zikula_AbstractInstaller::uninstall()
     * @return boolean True on success or false on failure.
     */
    public function uninstall()
    {
        $this->delVars();

        if (!DBUtil::dropTable('ldapauth_profile_mapping')) {
            return false;
        }
        if (!DBUtil::dropTable('ldapauth_adinfo')) {
            return false;
        }

        return true;
    }
}
