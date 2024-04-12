<?php

namespace cronv\Task\Management\Security;

use cronv\Task\Management\Constants\RouterConstants;
use cronv\Task\Management\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticator for handling login form submissions.
 *
 * @package cronv\Task\Management\Security
 */
class LoginFormAuth extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /** @var EntityManagerInterface EntityManager interface */
    private EntityManagerInterface $entityManager;

    /** @var UrlGeneratorInterface UrlGeneratorInterface is the interface that all URL generator classes must implement */
    private UrlGeneratorInterface $urlGenerator;

    /** @var CsrfTokenManagerInterface Manages CSRF tokens */
    private CsrfTokenManagerInterface $csrfTokenManager;

    /** @var UserPasswordHasherInterface Interface for the user password hasher service */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * LoginFormAuth constructor.
     *
     * @param EntityManagerInterface $entityManager Entity manager for database operations
     * @param UrlGeneratorInterface $urlGenerator URL generator for redirecting users
     * @param CsrfTokenManagerInterface $csrfTokenManager CSRF token manager for CSRF protection
     * @param UserPasswordHasherInterface $passwordHasher Password hasher for hashing and validating passwords
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === RouterConstants::LOGIN_ROUTE;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token', '');

        $request->getSession()->set(
            SecurityRequestAttributes::LAST_USERNAME,
            $username
        );

        $token = new CsrfToken('authenticate', $csrfToken);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }


        return new RedirectResponse($this->getLoginUrl($request));
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(RouterConstants::LOGIN_ROUTE);
    }
}
