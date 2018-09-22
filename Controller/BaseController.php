<?php

namespace Canabelle\CMSAnalyticsBundle\Controller;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as sfController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * Base controller for administration
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
abstract class BaseController extends sfController
{
    /**
     * The related Admin class
     *
     * @var AdminInterface
     */
    protected $admin;
    /**
     * @return boolean true if the request is done by an ajax like query
     */
    protected function isXmlHttpRequest()
    {
        return $this->get('request')->isXmlHttpRequest() || $this->get('request')->get('_xml_http_request');
    }
    /**
     * return the base template name
     *
     * @return string the template name
     */
    protected function getBaseTemplate()
    {
        if ($this->isXmlHttpRequest()) {
            return $this->admin->getTemplate('ajax');
        }
        return $this->admin->getTemplate('layout');
    }

    /**
     * @param $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     * @throws \Twig_Error
     */
    protected function renderResponse($view, array $parameters = [], Response $response = null)
    {
        $this->admin = $this->get('sonata.admin.pool');
        $parameters['base_template'] = isset($parameters['base_template']) ? $parameters['base_template'] : $this->getBaseTemplate();
        $parameters['admin_pool']    = $this->get('sonata.admin.pool');
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }
    /**
     * @param mixed   $data
     * @param integer $status
     * @param array   $headers
     *
     * @return Response with json encoded data
     */
    protected function renderJson($data, $status = 200, $headers = [])
    {
        // fake content-type so browser does not show the download popup when this
        // response is rendered through an iframe (used by the jquery.form.js plugin)
        //  => don't know yet if it is the best solution
        if ($this->get('request')->get('_xml_http_request')
            && strpos($this->get('request')->headers->get('Content-Type'), 'multipart/form-data') === 0) {
            $headers['Content-Type'] = 'text/plain';
        } else {
            $headers['Content-Type'] = 'application/json';
        }
        return new Response(json_encode($data), $status, $headers);
    }

    /**
     * Translate a message
     *
     * @param $message
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     * @return string
     */
    protected function trans($message, array $parameters = [], $domain = null, $locale = null)
    {
        if ($domain == null) {
            $domain = 'CanabelleCMSAnalyticsBundle';
        }
        /** @var Translator $translator */
        $translator = $this->get('translator');
        return $translator->trans($message, $parameters, $domain, $locale);
    }

    /**
     * Adds a flash message for type.
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')
            ->getFlashBag()
            ->add($type, $message);
    }
}