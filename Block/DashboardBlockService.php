<?php

namespace Canabelle\CMSAnalyticsBundle\Block;

use Canabelle\CMSAnalyticsBundle\Model\GoogleAnalyticsManager;
use Canabelle\CMSAnalyticsBundle\Model\Response\Row;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlockService extends BaseBlockService
{
    /** @var GoogleAnalyticsManager */
    protected $manager;

    public function __construct(string $name, EngineInterface $templating, GoogleAnalyticsManager $manager)
    {
        parent::__construct($name, $templating);

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'GoogleAnalyticsDashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $chartLabels = [];
        $chartNumbers = [];

        try {
            $monthVisits = $this->manager->getLastMonthVisits();

            /** @var Row $monthVisit */
            foreach ($monthVisits as $monthVisit) {
                $date = $monthVisit->getDate();
                $chartLabels[] = sprintf(
                    '%s. %s. %s',
                    round(substr($date, 6)),
                    round(substr($date, 4,2)),
                    round(substr($date, 0,4))
                );

                $chartNumbers[] = $monthVisit->getUsers();
            }
        } catch (\Google_Exception $e) {
        }

        return $this->renderResponse(
            'CanabelleCMSAnalyticsBundle:Block:block_dashboard.html.twig',
            [
                'block_context'  => $blockContext,
                'block'     => $blockContext->getBlock(),
                'blockId'   => 'block-google-analytics-dashboard',
                'chartLabels' => $chartLabels,
                'chartNumbers' => $chartNumbers,
            ] + $this->getDashboardViewParams(),
            $response
        );
    }

    /**
     * @return array
     */
    protected function getDashboardViewParams()
    {
        try {
            $today       = $this->manager->getToday();
            $yesterday   = $this->manager->getYesterday();
            $month       = $this->manager->getLastMonth();
        } catch (\Google_Exception $e) {
            return [
                'is_dummy'                      => $this->manager->isDummy(),
                'today_users'                   => 0,
                'today_visit'                   => 0,
                'today_page_view'               => 0,
                'today_page_per_visit'          => 0,
                'today_page_per_session'        => 0,
                'today_avg_time_on_site'        => '00:00:00',
                'today_visit_bounce_rate'       => 0,
                'today_new_visit'               => 0,
                'yesterday_users'               => 0,
                'yesterday_visit'               => 0,
                'yesterday_page_view'           => 0,
                'yesterday_page_per_visit'      => 0,
                'yesterday_page_per_session'    => 0,
                'yesterday_avg_time_on_site'    => '00:00:00',
                'yesterday_visit_bounce_rate'   => 0,
                'yesterday_new_visit'           => 0,
                'month_users'                   => 0,
                'month_visit'                   => 0,
                'month_page_view'               => 0,
                'month_page_per_visit'          => 0,
                'month_page_per_session'        => 0,
                'month_avg_time_on_site'        => '00:00:00',
                'month_visit_bounce_rate'       => 0,
                'month_new_visit'               => 0,
            ];
        }

        return [
            'is_dummy'                      => $this->manager->isDummy(),
            'today_users'                   => $today->getUsers(),
            'today_visit'                   => $today->getVisits(),
            'today_page_view'               => $today->getPageViews(),
            'today_page_per_visit'          => $today->getPageViewsPerVisit(),
            'today_page_per_session'        => $today->getPageViewsPerSession(),
            'today_avg_time_on_site'        => $today->getAvgTimeOnSite(),
            'today_visit_bounce_rate'       => $today->getVisitBounceRate(),
            'today_new_visit'               => $today->getNewVisits(),
            'yesterday_users'               => $yesterday->getUsers(),
            'yesterday_visit'               => $yesterday->getVisits(),
            'yesterday_page_view'           => $yesterday->getPageViews(),
            'yesterday_page_per_visit'      => $yesterday->getPageViewsPerVisit(),
            'yesterday_page_per_session'    => $yesterday->getPageViewsPerSession(),
            'yesterday_avg_time_on_site'    => $yesterday->getAvgTimeOnSite(),
            'yesterday_visit_bounce_rate'   => $yesterday->getVisitBounceRate(),
            'yesterday_new_visit'           => $yesterday->getNewVisits(),
            'month_users'                   => $month->getUsers(),
            'month_visit'                   => $month->getVisits(),
            'month_page_view'               => $month->getPageViews(),
            'month_page_per_visit'          => $month->getPageViewsPerVisit(),
            'month_page_per_session'        => $month->getPageViewsPerSession(),
            'month_avg_time_on_site'        => $month->getAvgTimeOnSite(),
            'month_visit_bounce_rate'       => $month->getVisitBounceRate(),
            'month_new_visit'               => $month->getNewVisits(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [];
    }
}
