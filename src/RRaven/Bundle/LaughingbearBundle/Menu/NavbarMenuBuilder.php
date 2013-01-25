<?php
namespace RRaven\Bundle\LaughingbearBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;

class NavbarMenuBuilder extends AbstractNavbarMenuBuilder
{
    protected $securityContext;
    protected $isLoggedIn;

    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext)
    {
        parent::__construct($factory);

        $this->securityContext = $securityContext;
        $this->isLoggedIn = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');

        //$menu->addChild('Shipdev', array('route' => 'shipdev'));

        //$dropdown = $this->createDropdownMenuItem($menu, "Mehr");
        //$dropdown->addChild('Captain Ränge', array('route' => 'revorix_ranks'));
        //$dropdown->addChild('Schiffs-XP', array('route' => 'revorix_xptool'));

        return $menu;
    }

    public function createRightSideDropdownMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav pull-right');

        if ($this->isLoggedIn) {
            $menu->addChild('Logout', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('Login', array('route' => 'hwi_oauth_connect'));
        }

        $this->addDivider($menu, true);
        //$menu->addChild('Impressum', array('route' => 'impressum'));

        return $menu;
    }
}