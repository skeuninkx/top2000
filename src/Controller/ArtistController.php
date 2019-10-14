<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArtistController
 *
 * @author Sjors Keuninkx <sjors.keuninkx@gmail.com>
 */
class ArtistController extends AbstractController
{
    /**
     * @Route("/")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }
}