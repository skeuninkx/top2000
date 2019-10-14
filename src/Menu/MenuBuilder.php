<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MenuBuilder
 *
 * @author Sjors Keuninkx <sjors.keuninkx@gmail.com>
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor
     *
     * @param FactoryInterface $factory
     * @param TranslatorInterface $translator
     */
    public function __construct(FactoryInterface $factory, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    /**
     * Setup main menu
     *
     * @return ItemInterface
     */
    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'vertical medium-horizontal menu',
                'data-responsive-menu' => 'accordion medium-dropdown',
            ]
        ]);

        $menu->addChild($this->translator->trans('menu.artist'), ['route' => 'app_artist_index']);
        $menu->addChild($this->translator->trans('menu.song'), ['route' => 'app_song_index']);

        return $menu;
    }

    /**
     * Setup footer menu
     *
     * @return ItemInterface
     */
    public function createFooterMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('footer', [
            'childrenAttributes' => [
                'class' => 'menu',
            ],
        ]);

        $menu->addChild($this->translator->trans('menu.about'), ['route' => 'app_page_about']);
        $menu->addChild($this->translator->trans('menu.contact'), ['route' => 'app_page_contact']);

        return $menu;
    }
}
