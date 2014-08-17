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
 * Administrative UI-oriented operations.
 */
class LDAPAuth_Controller_Admin extends LDAPAuth_AbstractController
{
    /**
     * The default entrypoint, redirects to view
     */
    public function main()
    {
        $this->redirect(ModUtil::url($this->name, 'admin', 'view'));
    }

    /**
     * Shows the Configuration
     *
     * @return string
     */
    public function view()
    {
        $modulevars = $this->getVars();

        // Assign the items to the template & return output
        return $this->view
            ->assign('modulevars', $modulevars)
            ->fetch('admin/view.tpl');
    }

    /**
     * Edit-page of the Configuration
     *
     * @return string
     */
    public function editConfig()
    {
        $modulevars = $this->getVars();

        // Assign the items to the tamplate & return output
        return $this->view
            ->assign('modulevars', $modulevars)
            ->fetch('admin/editconfig.tpl');
    }

    /**
     * Handles the saving Process of the Configuration.
     *
     * Redirects to view after finished
     */
    public function storeConfig()
    {
        $data = [
            'active'                => 0,
            'profile'               => 0,
            'account_suffix'        => null,
            'base_dn'               => null,
            'domain_controllers'    => null,
            'admin_username'        => null,
            'admin_password'        => null,
            'real_primarygroup'     => 0,
            'use_ssl'               => 0,
            'use_tsl'               => 0,
            'recursive_groups'      => 0,
            'ad_port'               => 389,
            'sso'                   => 0,
        ];
        $this->getPost($data);
        $this->checkCsrfToken();

        if ($this->request->isPost()) {
            ModUtil::apiFunc($this->name, 'Admin', 'saveServerConfig', $data);
        }

        $this->redirect(ModUtil::url($this->name, 'admin', 'view'));
    }

    /**
     * Shows all configured Profile-Mappings
     *
     * @return string
     */
    public function viewProfileMapping()
    {
        $items = [];
        $profileActive = false;

        // Is Profile-Extension active?
        if (ModUtil::available('Profile')) {
            $profileActive = true;
            $items = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAll');
        }

        $attributes = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAttributes');

        return $this->view
            ->assign('admin', $admin)
            ->assign('profileActive', $profileActive)
            ->assign('items', $items)
            ->assign('attributes', $attributes)
            ->fetch('admin/viewProfileMapping.tpl');
    }

    /**
     * Shows the formular to create a new Profile mapping
     *
     * @return string
     */
    public function addProfileMapping()
    {
        $properties = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getProperties');
        $attributes = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAttributes');

        return $this->view
            ->assign('properties', $properties)
            ->assign('attributes', $attributes)
            ->fetch('admin/addProfileMapping.tpl');
    }

    /**
     * Shows the formular to edit a Profile mapping
     *
     * @return string
     */
    public function editProfileMapping()
    {
        $data = ['id' => null];
        $this->getGet($data);
        $this->validator->checkNotNull($data, ['id']);

        $item = ModUtil::apiFunc($this->name, 'ProfileMapping', 'get', $data);
        $properties = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getProperties');
        $attributes = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAttributes');

        $this->assign2View($data);
        return $this->view
            ->assign('item', $item)
            ->assign('properties', $properties)
            ->assign('attributes', $attributes)
            ->fetch('admin/editProfileMapping.tpl');
    }

    /**
     * Processes the saving Process of a new or edited Profile mapping
     *
     * Redirects to viewProfileMapping after finished
     */
    public function storeProfileMapping()
    {
        $data = [
            'action'    => null,
            'id'        => null,
            'active'    => 0,
            'prop_id'   => null,
            'attribute' => null,
        ];
        $this->getPost($data);
        $this->checkCsrfToken();
        $this->validator->checkNotNull($data, ['action', 'prop_id', 'attribute']);

        if ($this->request->isPost()) {
            ModUtil::apiFunc($this->name, 'ProfileMapping', 'save', $data);
        }

        $this->redirect(ModUtil::url($this->name, 'admin', 'viewProfileMapping'));
    }

    /**
     * Shows the confirmation page for the deletion of a Profile mapping
     *
     * @return string
     */
    public function removeProfileMapping()
    {
        $data = ['id' => null];
        $this->getGet($data);
        $this->validator->checkNotNull($data, ['id']);

        $item = ModUtil::apiFunc($this->name, 'ProfileMapping', 'get', $data);
        $properties = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getProperties');
        $attributes = ModUtil::apiFunc($this->name, 'ProfileMapping', 'getAttributes');

        $this->assign2View($data);
        return $this->view
            ->assign('item', $item)
            ->assign('properties', $properties)
            ->assign('attributes', $attributes)
            ->fetch('admin/deleteProfileMapping.tpl');
    }

    /**
     * Processes the deletion of a Profile Mapping
     *
     * Redirects to viewProfileMapping after finished
     */
    public function deleteProfileMapping()
    {
        $data = ['id' => null];
        $this->getGet($data);
        $this->validator->checkNotNull($data, ['id']);

        if ($this->request->isPost()) {
            $this->checkCsrfToken();
            ModUtil::apiFunc($this->name, 'ProfileMapping', 'delete', $data);
        }

        $this->redirect(ModUtil::url($this->name, 'admin', 'viewProfileMapping'));
    }

    /**
     * Shows the selection page of users to update
     *
     * @return string
     */
    public function userUpdate()
    {
        // Get all users to update
        $adUsers2Update = ModUtil::apiFunc($this->name, 'user', 'getUsers2Update');

        return $this->view
            ->assign('users2update', $adUsers2Update)
            ->fetch('admin/userupdate.tpl');
    }

    /**
     * Handles the User updates
     *
     * @return string
     */
    public function updateADUsers()
    {
        $data = ['users' => []];
        $this->getPost($data);
        $this->checkCsrfToken();

        if (empty($data['users'])) {
            LogUtil::registerError($this->__('No Users to update selected!'));
            $this->redirect(ModUtil::url($this->name, 'admin', 'userUpdate'));
        }

        $this->validator->checkNotNull($data, ['users']);
        $this->throwForbiddenIf(!is_array($data['users']));

        return $this->view
            ->assign('items', json_encode($data['users']))
            ->fetch('admin/updateADUsers.tpl');
    }

    /**
     * Shows the selection page of users to import
     *
     * @return string
     */
    public function userImport()
    {
        // Get all Users to import
        $newADUserList = ModUtil::apiFunc($this->name, 'user', 'getUsers2Import');

        return $this->view
            ->assign('newadusers', $newADUserList)
            ->fetch('admin/userimport.tpl');
    }

    /**
     * Handles the User imports
     *
     * @return string
     */
    public function importUsers()
    {
        $data = ['users' => []];
        $this->getPost($data);
        $this->checkCsrfToken();

        if (empty($data['users'])) {
            LogUtil::registerError($this->__('No Users to import selected!'));
            $this->redirect(ModUtil::url($this->name, 'admin', 'userImport'));
        }

        $this->validator->checkNotNull($data, ['users']);
        $this->throwForbiddenIf(!is_array($data['users']));

        return $this->view
            ->assign('items', json_encode($data['users']))
            ->fetch('admin/importUsers.tpl');
    }

    /**
     * Shows selection of groups to import
     *
     * @return string
     */
    public function groupImport()
    {
        // Get all Groups to import
        $newADGroupList = ModUtil::apiFunc($this->name, 'Group', 'getGroups2Import');

        return $this->view
            ->assign('newadgroups', $newADGroupList)
            ->fetch('admin/groupimport.tpl');
    }

    /**
     * Handles the group imports
     *
     * @return string
     */
    public function importGroups()
    {
        $data = ['groups' => []];
        $this->getPost($data);
        $this->checkCsrfToken();

        if (empty($data['groups'])) {
            LogUtil::registerError($this->__('No groups to import selected!'));
            $this->redirect(ModUtil::url($this->name, 'admin', 'groupImport'));
        }

        $this->validator->checkNotNull($data, ['groups']);
        $this->throwForbiddenIf(!is_array($data['groups']));

        return $this->view
            ->assign('items', json_encode($data['groups']))
            ->fetch('admin/importGroups.tpl');
    }

    /**
     * Post initialise: called from constructor
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function postInitialize()
    {
        parent::postInitialize();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('LDAPAuth::', '::', ACCESS_ADMIN));
        $this->validator = new LDAPAuth_Validator_ControllerVal();

        // Disable caching by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }
}
