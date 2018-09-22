<?php

namespace Canabelle\CMSAnalyticsBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;

class DashboardBlockService extends BaseBlockService
{
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
        $settings = array_merge($this->getDefaultSettings(), $blockContext->getSettings());
        return $this->renderResponse(
            'CanabelleCMSAnalyticsBundle:Block:block_dashboard.html.twig',
            array(
                'block_context'  => $blockContext,
                'block'     => $blockContext->getBlock(),
                'blockId'   => 'block-google-analytics-dashboard',
                'settings'  => $settings
            ),
            $response
        );
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
