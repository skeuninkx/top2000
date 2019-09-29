<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 */
class IndexController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction()
    {
        return [];
    }
}