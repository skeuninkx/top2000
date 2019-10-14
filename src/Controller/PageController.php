<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SongController
 *
 * @author Sjors Keuninkx <sjors.keuninkx@gmail.com>
 */
class PageController extends AbstractController
{
    /**
     * @Route("/about")
     *
     * @return array
     */
    public function aboutAction()
    {
        return [];
    }

    /**
     * @Route("/contact")
     *
     * @return array
     */
    public function contactAction()
    {
        return [];
    }
}