<?php
    namespace KULeuven\ShibbolethBundle\Service;

    use Doctrine\ORM\EntityManager;
    use KULeuven\ShibbolethBundle\Security\ShibbolethUserProviderInterface;
    use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\User\UserProviderInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
    use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

    class ShibbolethUserProvider implements ShibbolethUserProviderInterface{
        private $entityManager;
        private $userClassFqcn;
        private $uniqueUserProperty;

        public function __construct(EntityManager $em, $userClassFqcn, $uniqueUserProperty){
            $this->entityManager = $em;
            $this->userClassFqcn = $userClassFqcn;
            $this->uniqueUserProperty = $uniqueUserProperty;
        }

        public function loadUserByUsername($username, $token = null){
            $user = $this->entityManager->getRepository($this->userClassFqcn)->findOneBy(array($this->uniqueUserProperty => $username));
            if($user){
                if($token instanceof ShibbolethUserToken){
                    $user->processNewShibbolethToken($token);
                    $this->entityManager->flush();
                }
                return $user;
            }
            else{
                throw new UsernameNotFoundException("User " . $username . " not found.");
            }
        }

        public function createUser(ShibbolethUserToken $token){
            $user = new $this->userClassFqcn;
            $user->setupWithShibbolethCredentials($token);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $user;
        }

        public function refreshUser(UserInterface $user){
            if (! $user instanceof $this->userClassFqcn){ throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user))); }
            return $this->loadUserByUsername($user->getUsername());
        }

        public function supportsClass($class){
            return $class === $this->userClassFqcn;
        }
    }