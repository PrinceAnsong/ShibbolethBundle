<?php

namespace KULeuven\ShibbolethBundle\Service;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ShibbolethUserInterface
 * @package KULeuven\ShibbolethBundle\Security
 * @author Prince Ansong, Teaching & Learning Department, KU Leuven.
 */
interface ShibbolethUserInterface extends UserInterface{

    /**
     * Perform initial setup based on the given Shibboleth token.
     * @param \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token The Shibboleth token containing the newly fetched data.
     */
    function setupWithShibbolethCredentials(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token);

    /**
     * Process newly fetched Shibboleth data.
     * @param \KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token The Shibboleth token containing the newly fetched data.
     */
    function processNewShibbolethToken(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token);

}
