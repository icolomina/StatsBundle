<?php

namespace Ict\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IctStatsBundle:Default:index.html.twig', array('name' => $name));
    }
}
