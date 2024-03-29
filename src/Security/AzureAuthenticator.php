<?php


namespace App\Security;


use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

class AzureAuthenticator extends SocialAuthenticator
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(RouterInterface $router, ClientRegistry $clientRegistry, UserRepository $userRepository, SessionInterface $session)
    {
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'azure_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->clientRegistry->getClient('azure'));
    }

    /**
     * @param AccessToken $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|void|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var AzureResourceOwner $azureUser */
        $azureUser = $this->clientRegistry->getClient('azure')->fetchUserFromToken($credentials);
        $domain = explode('@', $azureUser->getUpn())[1];

        if ($domain != 'epita.fr') {
            throw new CustomUserMessageAuthenticationException('<strong>' . $domain . '</strong> is not a valid email domain.');
        }

        return $this->userRepository->findOrCreateFromAzureOauth($azureUser);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        $this->session->getFlashBag()->add('danger', $message);
        return new RedirectResponse('/');
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse('/');
    }
}