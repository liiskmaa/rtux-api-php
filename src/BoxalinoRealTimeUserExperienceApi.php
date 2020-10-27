<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi;

use Boxalino\RealTimeUserExperienceApi\DependencyInjection\ApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class BoxalinoRealTimeUserExperienceApi
 *
 * @package Boxalino\RealTimeUserExperienceApi
 */
class BoxalinoRealTimeUserExperienceApi extends Bundle
{
    /**
     * @return Extension
     */
    public function getContainerExtension(): Extension
    {
        return new ApiExtension();
    }


}
