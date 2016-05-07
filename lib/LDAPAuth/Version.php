<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 */

/**
 * LDAPAuth module version information and other metadata.
 */
class LDAPAuth_Version extends Zikula_AbstractVersion
{
    /**
     * Returns the LDAPAuth Version Metadata
     *
     * @return array
     */
    public function getMetaData()
    {
        $meta = [
            'name' => 'LDAPAuth',
            'version' => '1.0.0',
            'url' => 'LDAPAuth',
            'displayname' => 'LDAPAuth',
            'description' => __('Provides LDAP authentication for Zikula user accounts with fallback if LDAP Server not avaiable.'),
            'capabilities' => [
                'authentication' => [
                    'version' => '1.0.0'
                ]
            ],
            'securityschema' => [
                'LDAPAuth::self' => '::',
                'LDAPAuth::' => 'User ID::',
            ],
            'core_min' => '1.3.5', // Fixed to 1.3.x range
            'core_max' => '1.4.99', // Fixed to 1.4.x range
        ];

        return $meta;
    }
}
