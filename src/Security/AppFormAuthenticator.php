<?php declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\{RedirectResponse, Request};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\{
    Core\Authentication\Token\TokenInterface,
    Core\Encoder\UserPasswordEncoderInterface,
    Core\Exception\InvalidCsrfTokenException,
    Core\Security,
    Core\User\UserInterface,
    Core\User\UserProviderInterface,
    Csrf\CsrfToken,
    Csrf\CsrfTokenManagerInterface,
    Guard\Authenticator\AbstractFormLoginAuthenticator,
    Guard\PasswordAuthenticatedInterface,
    Http\Util\TargetPathTrait
};

/**
 * Base application authenticator.
 */
class AppFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $userProvider->loadUserByUsername($credentials['email']);
    }

    /**
     * {@inheritDoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return $this->urlGenerator->generate('pizza_main');
    }

    /**
     * {@inheritDoc}
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
