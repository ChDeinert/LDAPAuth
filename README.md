# LDAPAuth

[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

A LDAP-Authentication Module for the *[Zikula Application Framework](http://www.zikula.org)* using [ADLDAP](https://github.com/adldap/adLDAP)


## Feature Overview

- Authentication of AD Users to log into Zikula
- Registration of AD Users in your _Zikula_ Application on first time log in
- Mass import/update of AD users
- Mass import of AD groups
- Update of the User and its group memberships on each log in
- Customizable Mapping to automatically fill in the _Profile_ modules' User informations with the AD data


## Requirements

- An active installation of the _Zikula Application Framework_ with version **&gt; 1.3.0** running on PHP 5.4 or higher
- [LDAP Extensions to PHP](http://www.php.net/ldap).
- An Active Directory domain controller to connect to.


## Installation

### Installation via Github
1. Go into the module path of your _Zikula_ installation.
2. Run following snippet to get the module

		git clone https://github.com/ChDeinert/LDAPAuth LDAPAuth
3. Initialize the **LDAPAuth** module in the _Zikula_ Adminstration area.
4. This one is optional but recommended: Goto the *Zikula Block* Administration and replace the original login-Block with the **LDAPAuth/Log-in block**.  

### Installation via Download
1. Download the module [here](http://github.com/ChDeinert/LDAPAuth/releases/latest) and extract the contents into a folder calles LDAPAuth.
1. Copy or move the folder into the module path of your _Zikula_ installation directory.
2. Initialize the **LDAPAuth** module in the _Zikula_ Administration area.
3. This one is optional but recommended: Goto the *Zikula Block* Administration and replace the original login-Block with the **LDAPAuth/Log-in block**.


## Configuration

The Configuration of the **LDAPAuth** module is found in the _Zikula_ Administration area.
**!Important!** You will need an AD User to connect to the AD. You should use one with **only reading permissions**!

**A Quick overview of important settings:**
- **Active Configuration**: Indicates whether the authentication via LDAP should be used.
- **Support Profile module**: Indicates whether the Profile module mapping should be used. (requires the Profile module to be installed and activated)
- **Account Suffix**: The full account suffix for your domain.
- **Base DN**: The base dn for your domain.
- **Domain Controllers**: A list of Domain Controllers separated with *,*.
- **Username**: The Searches an checks will be performed with this AD user account.
- **Password**: The corresponding password for Username.
- **Real Primarygroup**: Resolve the real primary group.
- **Use SSL**: Use SSL for connection.
- **Use TSL**: Use TSL for connection.
- **Recoursive groups**: When querying group membership, do it recursively.
- **AD Port**: Port used to talk to the domain controllers.
- **SSO**: To indicate to adLDAP to reuse password set by the brower through NTLM or Kerberos.


## License

LDAPAuth is open-source Software licensed under the [GNU General Public License (GPL) 3.0](http://www.gnu.org/licenses/gpl-3.0)
