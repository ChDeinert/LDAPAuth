<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 * @subpackage Controller
 */

/**
 * User UI-oriented operations.
 */
class LDAPAuth_Controller_User extends LDAPAuth_AbstractController
{
    /**
     * Users' Account view
     *
     * Shows the data that is copied from the Users' AD Profile
     *
     * @todo
     * @return string
     */
    public function viewADProfile()
    {
        $item = ModUtil::apiFunc($this->name, 'User', 'getUserinformation', ['uid' => UserUtil::getVar('uid')]);

        return $this->view
            ->assign('item', $item)
            ->fetch('user/viewADProfile.tpl');
    }

    /**
     * Post initialise: called from constructor
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function postInitialize()
    {
        parent::postInitialize();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_OVERVIEW));
    }
}
