<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Event\NestedEvent;

/**
 * Class ApiPageLoadedEvent
 * Boxalino API VIEW LOADED event
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Page
 */
class ApiPageLoadedEvent extends NestedEvent
{
    /**
     * @var ApiResponsePage
     */
    protected $page;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(ApiResponsePage $page, Request $request)
    {
        $this->page = $page;
        $this->request = $request;
    }

    public function getPage(): ApiResponsePage
    {
        return $this->page;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

}
