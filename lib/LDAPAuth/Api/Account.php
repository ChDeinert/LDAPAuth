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
 * Provides links for LDAPAuth on the "user account page"
 */
class LDAPAuth_Api_Account extends LDAPAuth_AbstractApi
{
    /**
     * Return an array of items to show in the the user's account panel.
     *
     * @todo
     * @return array Indexed array of items.
     */
    public function getAll()
    {
        $items = [];

        // Return the items
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
