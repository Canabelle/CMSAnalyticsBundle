<?php

namespace Canabelle\CMSAnalyticsBundle\Controller;

use Canabelle\CMSAnalyticsBundle\Model\GoogleAnalyticsManager;
use Canabelle\CMSAnalyticsBundle\Controller\BaseController as AdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AdminController
{
    /**
     * @return GoogleAnalyticsManager
     */
    protected function getManager()
    {
        return $this->get('canabelle_cms_analytics.manager.google_analytics');
    }

    /**
     * @return array
     */
    protected function getDashboardViewParams()
    {
        $manager    = $this->getManager();
        $today      = $manager->getToday();
        $yesterday  = $manager->getYesterday();
        return [
            'is_dummy' => $manager->isDummy(),
            'today_visit'               => $today->getVisits(),
            'today_page_view'           => $today->getPageViews(),
            'today_page_per_visit'      => $today->getPageViewsPerVisit(),
            'today_avg_time_on_site'    => $today->getAvgTimeOnSite(),
            'today_visit_bounce_rate'   => $today->getVisitBounceRate(),
            'today_new_visit'           => $today->getNewVisits(),
            'yesterday_visit'               => $yesterday->getVisits(),
            'yesterday_page_view'           => $yesterday->getPageViews(),
            'yesterday_page_per_visit'      => $yesterday->getPageViewsPerVisit(),
            'yesterday_avg_time_on_site'    => $yesterday->getAvgTimeOnSite(),
            'yesterday_visit_bounce_rate'   => $yesterday->getVisitBounceRate(),
            'yesterday_new_visit'           => $yesterday->getNewVisits(),
        ];
    }

    /**
     * @Route("/cms-analytics", name="cms_analytics")
     * @return Response
     * @throws \Twig_Error
     */
    public function indexAction()
    {
        return $this->renderResponse(
            'CanabelleCMSAnalyticsBundle:Default:index.html.twig',
            $this->getDashboardViewParams()
        );
    }
}