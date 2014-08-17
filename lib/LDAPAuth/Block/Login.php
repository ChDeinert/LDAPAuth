<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 * @subpackage Block
 */

/**
 * A block that allows users to log into the system.
 */
class LDAPAuth_Block_Login extends Zikula_Controller_AbstractBlock
{
    /**
     * Initialise interface.
     *
     * @see Zikula_Controller_AbstractBlock::init()
     */
    public function init()
    {
        SecurityUtil::registerPermissionSchema('Loginblock::', 'Block title::');
    }

    /**
     * Get info interface
     *
     * @see Zikula_Controller_AbstractBlock::info()
     */
    public function info()
    {
        return [
            'module'            => $this->name,
            'text_type'         => $this->__('Log-in'),
            'text_type_long'    => $this->__('Log-in block'),
            'allow_multiple'    => false,
            'form_content'      => false,
            'form_refresh'      => false,
            'show_preview'      => false,
        ];
    }

    /**
     * Display block.
     *
     * @see Zikula_Controller_AbstractBlock::display()
     * @param array $blockinfo
     * @return array
     */
    public function display($blockInfo)
    {
        $renderedOutput = '';

        if (SecurityUtil::checkPermission('Loginblock::', $blockInfo['title'].'::', ACCESS_READ)) {
            if (!UserUtil::isLoggedIn()) {
                if (empty($blockInfo['title'])) {
                    $blockInfo['title'] = DataUtil::formatForDisplay('Login');
                }

                $authenticationMethodList = new Users_Helper_AuthenticationMethodList($this);

                if ($authenticationMethodList->countEnabledForAuthentication() > 1) {
                    $selectedAuthenticationMethod = [
                        'modname' => 'LDAPAuth',
                        'method'  => 'ldap',
                    ];
                } else {
                    // There is only one (or there is none), so auto-select it.
                    $authenticationMethod = $authenticationMethodList->getAuthenticationMethodForDefault();
                    $selectedAuthenticationMethod = [
                        'modname' => $authenticationMethod->modname,
                        'method'  => $authenticationMethod->method,
                    ];
                }

                $authenticationMethodDisplayOrder = [];
                $authenticationMethodDisplayOrder[] = [
                    'modname' => $selectedAuthenticationMethod['modname'],
                    'method'  => $selectedAuthenticationMethod['method'],
                ];

                $this->view
                    ->assign('authentication_method_display_order', $authenticationMethodDisplayOrder)
                    ->assign('selected_authentication_method', $selectedAuthenticationMethod);

                // If the current page was reached via a POST or FILES then we don't want to return here.
                // Only return if the current page was reached via a regular GET
                if ($this->request->isGet()) {
                    $this->view->assign('returnpage', System::getCurrentUri());
                } else {
                    $this->view->assign('returnpage', '');
                }

                $tplName = mb_strtolower("block/login_{$blockInfo['position']}.tpl");

                if ($this->view->template_exists($tplName)) {
                    $blockInfo['content'] = $this->view->fetch($tplName);
                } else {
                    $blockInfo['content'] = $this->view->fetch('block/login.tpl');
                }

                $renderedOutput = BlockUtil::themeBlock($blockInfo);
            }
        }

        return $renderedOutput;
    }

    /**
     * Post initialise: called from constructor
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function postInitialize()
    {
        parent::postInitialize();

        // Disable caching by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }
}
