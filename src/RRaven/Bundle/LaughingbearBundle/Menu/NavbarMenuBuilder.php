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
        global $kernel;
        /* @var $kernel \AppKernel */
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');
        
        $menumenu =  $kernel->getContainer()->get("rraven.helper.menumenu");
        /* @var $menumenu \RRaven\Bundle\LaughingbearBundle\Helper\MenuMenuHelper */
        $menu_array = $menumenu->sniffMenus();
        
        
        
        
        
        $dropdown = $this->createDropdownMenuItem($menu, "More");
        $dropdown->addChild("buzztest", array("route" => "rraven_laughingbear_default_buzztest"));
        
        $dropdown_two = $this->createDropdownMenuItem($dropdown, "Dropdown");
        $dropdown_two->addChild("anothertest", array("route" => "rraven_laughingbear_default_buzztest"));

        //$menu->addChild('Shipdev', array('route' => 'shipdev'));

//        $dropdown = $this->createDropdownMenuItem($menu, "Mehr");
//        $dropdown->addChild('Captain Ränge', array('route' => 'rraven_laughingbear_default_buzztest'));
//        $dropdown->addChild('Schiffs-XP', array('route' => 'rraven_laughingbear_default_buzztest'));

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

        //$this->addDivider($menu, true);
        //$menu->addChild('Impressum', array('route' => 'impressum'));

        return $menu;
    }
}