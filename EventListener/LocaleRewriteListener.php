<?php

namespace Mdespeuilles\LanguageBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouteCollection;

class LocaleRewriteListener implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var array
     */
    private $supportedLocales;

    /**
     * @var string
     */
    private $localeRouteParam;

    /**
     * @var ContainerInterface
     */
    private $containerInterface;

    public function __construct(ContainerInterface $containerInterface, RouterInterface $router, $defaultLocale = 'en', $supportedLocales = 'en', $localeRouteParam = '_locale')
    {
        $this->router = $router;
        $this->routeCollection = $router->getRouteCollection();
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
        $this->localeRouteParam = $localeRouteParam;
        $this->containerInterface = $containerInterface;
    }

    public function isLocaleSupported($locale)
    {
        $supportedLocales = explode("|", $this->supportedLocales);
        return in_array($locale, $supportedLocales);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if ($path == "/") {
            $locale = $request->getPreferredLanguage(explode("|", $this->supportedLocales));
            //dump($locale);
            //if ($locale == "fr_FR") $locale = "fr";
            if ($locale == "" || $this->isLocaleSupported($locale) == false) {
                $locale = $request->getDefaultLocale();
            }

            $prefix = ($this->containerInterface->get('kernel')->getEnvironment() == "dev") ? "/app_dev.php" : null;
            $event->setResponse(new RedirectResponse($prefix."/".$locale.$path));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
