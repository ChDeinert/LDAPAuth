<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 */

/**
 * Gets table information for the LDAPAuth module.
 *
 * Returns (legacy) table information for the LDAPAuth module.
 *
 * @return array
 */
function LDAPAuth_tables()
{
    // Initialise table array
    $tables = [];

    // Table for Profile-Module mapping
    $tables['ldapauth_profile_mapping'] = 'ldapauth_profile_mapping';
    $tables['ldapauth_profile_mapping_column'] = [
        'id'        => 'id',
        'active'    => 'active',
        'prop_id'   => 'prop_id',
        'attribute' => 'attribute',
    ];
    $tables['ldapauth_profile_mapping_column_def'] = [
        'id'        => "I UNSIGNED NOTNULL AUTO PRIMARY",
        'active'    => "I1 NOTNULL DEFAULT 0",
        'prop_id'   => "I4 NOTNULL",
        'attribute' => "C(20) NOTNULL DEFAULT ''",
    ];

    // Table for AD User Infos
    $tables['ldapauth_adinfo'] = 'ldapauth_adinfo';
    $tables['ldapauth_adinfo_column'] = [
        'uid'                       => 'uid',
        'lastupdated'               => 'lastupdated',
        'cn'                        => 'cn',
        'sn'                        => 'sn',
        'telephonenumber'           => 'telephonenumber',
        'facsimiletelephonenumber'  => 'facsimiletelephonenumber',
        'givenname'                 => 'givenname',
        'distinguishedname'         => 'distinguishedname',
        'whencreated'               => 'whencreated',
        'whenchanged'               => 'whenchanged',
        'displayname'               => 'displayname',
        'department'                => 'department',
        'company'                   => 'company',
        'name'                      => 'name',
        'countrycode'               => 'countrycode',
        'samaccountname'            => 'samaccountname',
        'userprincipalname'         => 'userprincipalname',
        'objectcategory'            => 'objectcategory',
        'mail'                      => 'mail',
        'manager'                   => 'manager',
    ];
    $tables['ldapauth_adinfo_column_def'] = [
        'uid'                       => "I UNSIGNED NOTNULL PRIMARY",
        'lastupdated'               => "C(255) DEFAULT ''",
        'cn'                        => "C(255) DEFAULT ''",
        'sn'                        => "C(255) DEFAULT ''",
        'telephonenumber'           => "C(255) DEFAULT ''",
        'facsimiletelephonenumber'  => "C(255) DEFAULT ''",
        'givenname'                 => "C(255) DEFAULT ''",
        'distinguishedname'         => "C(255) DEFAULT ''",
        'whencreated'               => "C(255) DEFAULT ''",
        'whenchanged'               => "C(255) DEFAULT ''",
        'displayname'               => "C(255) DEFAULT ''",
        'department'                => "C(255) DEFAULT ''",
        'company'                   => "C(255) DEFAULT ''",
        'name'                      => "C(255) DEFAULT ''",
        'countrycode'               => "C(255) DEFAULT ''",
        'samaccountname'            => "C(255) DEFAULT ''",
        'userprincipalname'         => "C(255) DEFAULT ''",
        'objectcategory'            => "C(255) DEFAULT ''",
        'mail'                      => "C(255) DEFAULT ''",
        'manager'                   => "C(255) DEFAULT ''",
    ];

    return $tables;
}

