<?php

namespace KULeuven\ShibbolethBundle\Model;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;
use KULeuven\ShibbolethBundle\Service\ShibbolethUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * A standard Shibboleth user
 *
 * @author Prince Ansong
 */

abstract class ShibbolethUser implements ShibbolethUserInterface{

    /**
     * Process newly fetched Shibboleth data.
     * @param \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token The Shibboleth token containing the newly fetched data.
     */
    function processNewShibbolethToken(ShibbolethUserToken $token){
        // optional implementation in subclass
    }

    /**
     * Perform initial setup based on the given Shibboleth token.
     * @param \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token The Shibboleth token containing the newly fetched data.
     *
    function setupWithShibbolethCredentials(ShibbolethUserToken $token){
        // optional implementation in subclass
    }*/

    /**
     * @return null
     */
    public function getPassword() {
        return null;
    }

    /**
     * @return null
     */
    public function getSalt() {
        return null;
    }

    public function eraseCredentials() {
        // do nothing. no sensitive data is stored
    }

}