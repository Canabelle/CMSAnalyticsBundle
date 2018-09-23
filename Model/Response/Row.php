<?php

namespace Canabelle\CMSAnalyticsBundle\Model\Response;

class Row
{
    /**
     * Raw data returned by google analytics
     *
     * @var array
     */
    protected $properties = [];

    public function __construct($properties)
    {
        $defaults = [
            'ga:pageviews'          => 0,
            'ga:pageviewsPerSession'=> 0,
            'ga:sessionsPerUser'    => 0,
            'ga:sessions'           => 0,
            'ga:users'              => 0,
            'ga:newUsers'           => 0,
            'ga:avgSessionDuration' => 0,
            'ga:bounceRate'         => 0,
        ];

        $this->properties = $properties + $defaults;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        if (isset($this->properties['ga:date'])) {
            return $this->properties['ga:date'];
        } else {
            return '';
        }
    }

    /**
     * @return int
     */
    public function getUsers()
    {
        return $this->properties['ga:users'];
    }

    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->properties['ga:sessions'];
    }

    /**
     * @return int
     */
    public function getNewVisits()
    {
        return $this->properties['ga:newUsers'];
    }

    /**
     * @return int
     */
    public function getPageViews()
    {
        return $this->properties['ga:pageviews'];
    }

    /**
     * @return int
     */
    public function getPageViewsPerSession()
    {
        return round($this->properties['ga:pageviewsPerSession'], 2);
    }

    /**
     * @return float
     */
    public function getPageViewsPerVisit()
    {
        return round($this->properties['ga:sessionsPerUser'], 2);
    }

    /**
     * @return string
     */
    public function getAvgTimeOnSite()
    {
        $seconds = $this->properties['ga:avgSessionDuration'];
        $hours   = floor($seconds / (60 * 60));
        $minutes = floor(($seconds - ($hours * 60 * 60)) / 60);
        $seconds = $seconds - ($minutes * 60) - ($hours * 60 * 60);
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * @return float
     */
    public function getVisitBounceRate()
    {
        return round($this->properties['ga:bounceRate'], 2);
    }
}
