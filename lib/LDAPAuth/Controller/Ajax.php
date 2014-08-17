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
 * AJAX query and response functions.
 */
class LDAPAuth_Controller_Ajax extends Zikula_Controller_AbstractAjax
{
    /**
     * Starts the update of the selected Users
     *
     * @return Zikula_Response_Ajax
     */
    public function updateADUser()
    {
        $this->checkAjaxToken();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_ADMIN));

        $uid = $this->request->request->get('uid', null);
        ModUtil::apiFunc($this->name, 'User', 'updateInformation', ['uid' => $uid]);

        return new Zikula_Response_Ajax(['result' => true]);
    }

    /**
     * Starts the import of the selected AD-Users
     *
     * @return Zikula_Response_Ajax
     */
    public function importADUser()
    {
        $this->checkAjaxToken();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_ADMIN));

        $uname = $this->request->request->get('uname', null);
        ModUtil::apiFunc($this->name, 'User', 'importUser', ['uname' => $uname]);

        return new Zikula_Response_Ajax(['result' => true]);
    }

    /**
     * Starts the import of the selected AD-Groups
     *
     * @return Zikula_Response_Ajax
     */
    public function importGroup()
    {
        $this->checkAjaxToken();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_ADMIN));

        $group = $this->request->request->get('group', null);
        ModUtil::apiFunc($this->name, 'Group', 'importGroup', ['group' => $group]);

        return new Zikula_Response_Ajax(['result' => true]);
    }
}
