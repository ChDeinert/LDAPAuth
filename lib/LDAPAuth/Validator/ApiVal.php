<?php
/**
 * LDAPAuth module for Zikula
 *
 * @author Christian Deinert
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License (GPL) 3.0
 * @package LDAPAuth
 * @subpackage Validator
 */

/**
 * Abstract Validator for Api Classes
 */
class LDAPAuth_Validator_ApiVal
{
    private $name = 'LDAPAuth';

    /**
     * Prüft, ob die values zu den gegebenen keys im argsArray gesetzt und nicht leer sind
     *
     * @param array $argsArray
     * @param array $keys
     */
    public function hasValues(array &$argsArray, array $keys)
    {
        $this->checkIsset($argsArray, $keys);
        $this->checkNotEmpty($argsArray, $keys);
    }

    /**
     * Prüft, ob die values zu den gegebenen keys im argsArray gesetzt sind
     *
     * @param array $argsArray
     * @param array $keys
     * @throws Zikula_Exception_Fatal
     * @return boolean
     */
    private function checkIsset(array &$argsArray, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $argsArray)) {
                if (!isset($argsArray[$key])) {
                    throw new Zikula_Exception_Fatal();
                } else {
                    return true;
                }
            } else {
                throw new Zikula_Exception_Fatal();
            }
        }
    }

    /**
     * Prüft, ob die values zu den gegebenen keys im argsArray nicht leer sind
     *
     * @param array $argsArray
     * @param array $keys
     * @throws Zikula_Exception_Fatal
     * @return boolean
     */
    private function checkNotEmpty(array &$argsArray, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $argsArray)) {
                if (!isset($argsArray[$key])) {
                    throw new Zikula_Exception_Fatal();
                } else {
                    return true;
                }
            } else {
                throw new Zikula_Exception_Fatal();
            }
        }
    }
}
