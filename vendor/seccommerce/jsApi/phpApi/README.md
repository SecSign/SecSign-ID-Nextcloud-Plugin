# SecSign ID PHP Interface


**Overview**

SecSign ID Api is a two-factor authentication for PHP web applications.

This PHP API allows a secure login using a private key on a smart phone running SecSign ID by SecSign Technologies Inc.


**Usage**

* Include the PHP API `SecSignIDApi.php` in your project.
* Request an authentication session
* Show access pass to user and save session parameters 
* Get session state 
* React to the state and have the user logged in


Check out the included example `test.php` to see how it works or 
have a look at the how to use tutorial <https://www.secsign.com/php-tutorial/>
or visit <https://www.secsign.com> for more information.

**Files**

* `SecSignIDApi.php` - the file contains two classes SecSignIDApi and AuthSession. The class SecSignIDApi will care about the communication with the ID server
* `example.php` - a small test script
* `curl-ca-bundle.crt` - the allowed CA certificates

**Info**

The SecSign ID PHP Api is also used in the wordpress plugin <http://wordpress.org/plugins/secsign/> 
as well as for the php bridge in the SecSign ID Javascript Api <https://github.com/SecSign/secsign-js-api>.

For further information about the wordpress plugin see the tutorial <https://www.secsign.com/wordpress-tutorial/>


===============

SecSign Technologies Inc. official site: <https://www.secsign.com>
