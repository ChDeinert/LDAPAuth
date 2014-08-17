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
 * Access to user-initiated authentication actions for the LDAPAuth module.
 */
class LDAPAuth_Controller_Authentication extends Zikula_Controller_AbstractAuthentication
{
    /**
     * Render and return the portion of the HTML log-in form containing the fields needed by this authentication module in order to log in.
     *
     * @see Zikula_Controller_AbstractAuthentication::getLoginFormFields()
     */
    public function getLoginFormFields(array $args)
    {
        if (!isset($args) || !is_array($args)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'$args\' parameter was received.', 'Zikula'));
        }
        if (!isset($args['form_type']) || !is_string($args['form_type'])) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'An invalid form type (\'%1$s\') was received.',
                    [isset($args['form_type']) ? $args['form_type'] : 'NULL'],
                    'Zikula'
                )
            );
        }
        if (!isset($args['method']) ||
            !is_string($args['method']) ||
            !$this->supportsAuthenticationMethod($args['method'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'An invalid method (\'%1$s\') was received.',
                    [isset($args['method']) ? $args['method'] : 'NULL'],
                    'Zikula'
                )
            );
        }
        if ($this->authenticationMethodIsEnabled($args['method'])) {
            $templateName = mb_strtolower("auth/loginformfields_{$args['form_type']}_{$args['method']}.tpl");

            if (!$this->view->template_exists($templateName)) {
                $templateName = mb_strtolower("auth/loginformfields_default_{$args['method']}.tpl");

                if (!$this->view->template_exists($templateName)) {
                    $templateName = mb_strtolower("auth/loginformfields_{$args['form_type']}_default.tpl");

                    if (!$this->view->template_exists($templateName)) {
                        $templateName = mb_strtolower("auth/loginformfields_default_default.tpl");

                        if (!$this->view->template_exists($templateName)) {
                            throw new Zikula_Exception_Fatal(
                                $this->__f(
                                    'A form fields template was not found for the %1$s method using form type \'%2$s\'.',
                                    [$method, $args['form_type']],
                                    'Zikula'
                                )
                            );
                        }
                    }
                }
            }

            return $this->view
                ->assign('authentication_method', $args['method'])
                ->fetch($templateName);
        }
    }

    /**
     * Render and return an authentication method selector for the login page form or login block form.
     *
     * @see Zikula_Controller_AbstractAuthentication::getAuthenticationMethodSelector()
     */
    public function getAuthenticationMethodSelector(array $args)
    {
        if (!isset($args) || !is_array($args)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'$args\' parameter was received.', 'Zikula'));
        }
        if (!isset($args['form_type']) || !is_string($args['form_type'])) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'An invalid form type (\'%1$s\') was received.',
                    [isset($args['form_type']) ? $args['form_type'] : 'NULL'],
                    'Zikula'
                )
            );
        }
        if (!isset($args['form_action']) || !is_string($args['form_action'])) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'An invalid form action (\'%1$s\') was received.',
                    [isset($args['form_action']) ? $args['form_action'] : 'NULL'],
                    'Zikula'
                )
            );
        }
        if (!isset($args['method']) ||
            !is_string($args['method']) ||
            !$this->supportsAuthenticationMethod($args['method'])
        ) {
            throw new Zikula_Exception_Fatal(
                $this->__f(
                    'Error: An invalid method (\'%1$s\') was received.',
                    [isset($args['method']) ? $args['method'] : 'NULL'],
                    'Zikula'
                )
            );
        }
        if ($this->authenticationMethodIsEnabled($args['method'])) {
            $templateVars = [
                'authentication_method' => [
                    'modname' => $this->name,
                    'method'  => $args['method'],
                ],
                'is_selected' => isset($args['is_selected']) && $args['is_selected'],
                'form_type'   => $args['form_type'],
                'form_action' => $args['form_action'],
            ];
            $templateName = mb_strtolower("auth/authenticationmethodselector_{$args['form_type']}_{$args['method']}.tpl");

            if (!$this->view->template_exists($templateName)) {
                $templateName = mb_strtolower("auth/authenticationmethodselector_default_{$args['method']}.tpl");

                if (!$this->view->template_exists($templateName)) {
                    $templateName = mb_strtolower("auth/authenticationmethodselector_{$args['form_type']}_default.tpl");

                    if (!$this->view->template_exists($templateName)) {
                        $templateName = mb_strtolower("auth/authenticationmethodselector_default_default.tpl");

                        if (!$this->view->template_exists($templateName)) {
                            throw new Zikula_Exception_Fatal(
                                $this->__f(
                                    'An authentication method selector template was not found for method \'%1$s\' using form type \'%2$s\'.',
                                     [$args['method'], $args['form_type']]
                                )
                            );
                        }
                    }
                }
            }

            return $this->view
                ->assign($templateVars)
                ->fetch($templateName);
        }
    }

    /**
     * Performs initial user-interface level validation on the authentication information received by the user from the login process.
     *
     * @see Zikula_Controller_AbstractAuthentication::validateAuthenticationInformation()
     */
    public function validateAuthenticationInformation(array $args)
    {
        $validates = false;
        $authenticationMethod = isset($args['authenticationMethod']) ? $args['authenticationMethod'] : [];
        $authenticationInfo   = isset($args['authenticationInfo']) ? $args['authenticationInfo'] : [];

        // No need to be too fancy or too specific here. If the login id (the uname or email) is not empty, then that's sufficient.
        // If we are too specific here, then we are giving a potential hacker too much information about how the authentication process
        // works and what is expected. Just validate it enough so that a lookup can be performed.
        if (!empty($authenticationInfo['login_id'])) {
            if (!empty($authenticationInfo['pass'])) {
                $validates = true;
            } else {
                $this->registerError($this->__('Please provide a password.'));
            }
        } elseif (empty($authenticationInfo['pass'])) {
            if ($authenticationMethod['method'] == 'ldap') {
                $this->registerError($this->__('Please provide a user name and password.'));
            }
        } else {
            if ($authenticationMethod['method'] == 'ldap') {
                $this->registerError($this->__('Please provide a user name.'));
            }
        }

        return $validates;
    }

    /**
     * Post initialise: called from constructor.
     *
     * @see Zikula_AbstractBase::postInitialize()
     */
    protected function postInitialize()
    {
        parent::postInitialize();

        $this->validator = new LDAPAuth_Validator_ControllerVal();
    }
}
