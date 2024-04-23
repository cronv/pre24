<?php

namespace cronv\Task\Management\Middleware;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Middleware for checking user authentication before opening controllers
 */
class AuthMiddleware
{
    /** @var array List of allowed controllers */
    protected array $allowedControllers = [
        'TaskController',
        'SurveyController',
    ];

    /**
     * AuthMiddleware constructor
     *
     * @param Security $security The security service
     */
    public function __construct(
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    /**
     * Event listener for kernel.controller event.
     *
     * @param ControllerEvent $event The controller event.
     * @throws AccessDeniedException If access is denied.
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Controller name
        $controllerName = is_array($controller) ? get_class($controller[0]) : get_class($controller);
        $shortClassName = basename(str_replace('\\', '/', $controllerName));

        // Check if the controller is one of the allowed controllers
        if (!in_array($shortClassName, $this->getAllowedControllers())
            || $this->isAuth()
        ) {
            return;
        }

        // Check authentication
        // Generate the URL for the desired route
        $redirectUrl = $this->urlGenerator->generate('app_login');

        // Create a RedirectResponse to the desired route
        $response = new RedirectResponse($redirectUrl);

        // Set the response to the event
        $event->setController(fn() => $response);
    }

    /**
     * Checks if the user is authenticated.
     *
     * This method checks if the user is authenticated by verifying if they have the "IS_AUTHENTICATED_FULLY" role
     * or if there is a user object available.
     *
     * @return bool
     */
    protected function isAuth(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_FULLY') || $this->security->getUser();
    }

    /**
     * Get the list of allowed controllers.
     *
     * @return array The allowed controllers.
     */
    protected function getAllowedControllers(): array
    {
        return $this->allowedControllers;
    }
}
