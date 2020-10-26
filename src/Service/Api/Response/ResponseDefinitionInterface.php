<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\AccessorHandlerInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

interface ResponseDefinitionInterface
{

    const BOXALINO_PARAMETER_BX_VARIANT_UUID="_bx_variant_uuid";
    const BOXALINO_PARAMETER_CORRECTED_SEARCH_QUERY="correctedSearchQuery";
    const BOXALINO_PARAMETER_MAIN_HIT_COUNT="mainHitCount";
    const BOXALINO_PARAMETER_REDIRECT_URL="redirect_url";
    const BOXALINO_PARAMETER_HAS_SEARCH_SUBPHRASES="hasSearchSubPhrases";
    const BOXALINO_PARAMETER_BX_REQUEST_ID="_bx_request_id";
    const BOXALINO_PARAMETER_BX_GROUP_BY="_bx_group_by";

    /**
     * @return int
     */
    public function getHitCount() : int;

    /**
     * @return string|null
     */
    public function getRedirectUrl();

    /**
     * @return bool
     */
    public function isCorrectedSearchQuery() : bool;

    /**
     * @return string|null
     */
    public function getCorrectedSearchQuery();

    /**
     * @return bool
     */
    public function hasSearchSubPhrases() : bool;

    /**
     * @return string
     */
    public function getRequestId() : string;

    /**
     * @return string
     */
    public function getGroupBy() : string;

    /**
     * @return string
     */
    public function getVariantId() : string;

    /**
     * @return \ArrayIterator
     */
    public function getBlocks() : \ArrayIterator;

    /**
     * Debug and performance information
     *
     * @return array
     */
    public function getAdvanced() : array;

    /**
     * @return Response
     */
    public function getResponse() : Response;

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response);

    /**
     * @return string
     */
    public function getJson() : string;

    /**
     * @param \StdClass $data
     * @param AccessorInterface $model
     * @return mixed
     */
    public function toObject(\StdClass $data, AccessorInterface $model);

    /**
     * @return AccessorHandlerInterface
     */
    public function getAccessorHandler(): AccessorHandlerInterface;

    /**
     * Adding response positions for your integration
     * (ex: top, left, bottom, right,..)
     * This will make your content accessible in a structured way as the default "blocks" are accessed
     *
     * @param array $positions
     * @return mixed
     */
    public function addResponseSegments(array $positions);

    /**
     * @return array|string[]
     */
    public function getResponseSegments() : array;

}
