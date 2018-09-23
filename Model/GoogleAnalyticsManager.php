<?php

namespace Canabelle\CMSAnalyticsBundle\Model;

use Canabelle\CMSAnalyticsBundle\Model\Response\Row;

class GoogleAnalyticsManager
{
    //used to determine if configuration is set or not
    const DUMMY_PROFILE_ID = 'ga:YOUR_PROFIL_ID';

    /**
     * @var string
     */
    protected $profileId;

    /**
     * @var string
     */
    protected $privateKeyFile;

    /**
     * @var \Google_Service_Analytics
     */
    protected $service;

    /**
     * @param string $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * @param string $privateKeyFile
     */
    public function setPrivateKeyFile($privateKeyFile)
    {
        $this->privateKeyFile = $privateKeyFile;
    }

    /**
     * @return \Google_Service_Analytics
     * @throws \Google_Exception
     */
    protected function getService()
    {
        if ($this->service == null) {
            $client = new \Google_Client();
            $client->setApplicationName("Hello Analytics Reporting");
            $client->setAuthConfig($this->privateKeyFile);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            $this->service = new \Google_Service_Analytics($client);
        }
        return $this->service;
    }

    /**
     * Return base metrics for detailed queries
     *
     * @return array
     */
    protected function getMetrics()
    {
        return [
            'ga:pageviews',
            'ga:pageviewsPerSession',
            'ga:sessionsPerUser',
            'ga:sessions',
            'ga:users',
            'ga:newUsers',
            'ga:avgSessionDuration',
            'ga:bounceRate'
        ];
    }

    /**
     * Check if configuration is set or if we are in fake mode
     *
     * @return bool
     */
    public function isDummy()
    {
        return ($this->profileId == self::DUMMY_PROFILE_ID);
    }

    /**
     * @return Row
     * @throws \Google_Exception
     */
    public function getToday()
    {
        return $this->doQuery([
            'dimensions'    => ['ga:date'],
            'metrics'       => $this->getMetrics(),
            'start-date'    => date('Y-m-d'),
            'end-date'      => date('Y-m-d')
        ]);
    }

    /**
     * @return Row
     * @throws \Google_Exception
     */
    public function getYesterday()
    {
        return $this->doQuery([
            'dimensions'    => ['ga:date'],
            'metrics'       => $this->getMetrics(),
            'start-date'    => date('Y-m-d', strtotime('yesterday')),
            'end-date'      => date('Y-m-d', strtotime('yesterday'))
        ]);
    }

    /**
     * @return Row
     * @throws \Google_Exception
     */
    public function getLastMonth()
    {
        return $this->doQuery([
            'dimensions'    => [],
            'metrics'       => $this->getMetrics(),
            'start-date'    => date('Y-m-d', strtotime('30 days ago')),
            'end-date'      => date('Y-m-d', strtotime('yesterday'))
        ]);
    }

    /**
     * @return Row[]
     * @throws \Google_Exception
     */
    public function getLastMonthVisits()
    {
        return $this->doQuery([
            'dimensions'    => ['ga:date'],
            'metrics'       => ['ga:users'],
            'start-date'    => date('Y-m-d', strtotime('30 days ago')),
            'end-date'      => date('Y-m-d', strtotime('yesterday'))
        ], true);
    }

    /**
     * Request google analytics
     *
     * @param array $parameters
     * @param bool $multipleResults
     * @return Row|Row[]|mixed
     * @throws \Google_Exception
     */
    protected function doQuery(array $parameters, $multipleResults = false)
    {
        $parameters += [
            'dimensions'  => [],
            'metrics'     => [],
            'start-date'  => null,
            'end-date'    => null,
            'start-index' => 1,
            'max-results' => 100,
            'output'      => 'json'
        ];

        $response = $this->getService()->data_ga->get(
            'ga:' . $this->profileId,
            $parameters['start-date'],
            $parameters['end-date'],
            implode(',', $parameters['metrics']),
            [
                'dimensions' => implode(',', $parameters['dimensions']),
                'start-index' => $parameters['start-index'],
                'max-results' => $parameters['max-results'],
                'output' => $parameters['output'],
            ]
        );

        $results = $this->formatResponse($response, array_merge($parameters['dimensions'], $parameters['metrics']));

        if ($multipleResults == false) {
            return array_shift($results);
        }

        return $results;
    }

    /**
     * @param  \Google_Service_Analytics_GaData $response
     * @param  array    $cols
     * @return Row[]
     */
    protected function formatResponse(\Google_Service_Analytics_GaData $response, array $cols)
    {
        $results = [];

        foreach ($response->getRows() as $row) {
            $results[] = new Row(array_combine($cols, $row));
        }

        return $results;
    }
}