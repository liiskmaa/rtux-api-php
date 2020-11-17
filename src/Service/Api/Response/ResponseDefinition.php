<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\AccessorHandlerInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\UndefinedPropertyError;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ResponseDefinition
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response
 */
class ResponseDefinition implements ResponseDefinitionInterface
{

    use ResponseHydratorTrait;

    /**
     * If the facets are declared on a certain position, they are isolated in a specific block
     * All the other content is located under "blocks"
     */
    const BOXALINO_RESPONSE_POSITION = ["left", "right", "sidebar", "top", "bottom"];

    /**
     * @var string
     */
    protected $json;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var null | \StdClass
     */
    protected $data = null;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AccessorHandlerInterface
     */
    protected $accessorHandler = null;

    /**
     * @var null | \ArrayIterator
     */
    protected $blocks = null;

    /**
     * The visual elements declared with a property "position" different than "main" or none, will have their own
     * RESPONSE SEGMENT by which they can be accessed
     *
     * @var array
     */
    protected $segments = ["top", "right", "bottom", "left"];

    public function __construct(LoggerInterface $logger, AccessorHandlerInterface $accessorHandler)
    {
        $this->logger = $logger;
        $this->accessorHandler = $accessorHandler;
    }

    /**
     * Allows accessing other parameters
     *
     * @param string $method
     * @param array $params
     * @return array
     */
    public function __call(string $method, array $params = [])
    {
        preg_match('/^(get)(.*?)$/i', $method, $matches);
        $prefix = $matches[1] ?? '';
        $element = $matches[2] ?? '';
        $element = strtolower($element);
        if ($prefix == 'get')
        {
            return $this->getContentByType($element);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getHitCount() : int
    {
        try{
            try {
                if(property_exists($this->get()->system, "mainHitCount"))
                {
                    return $this->get()->system->mainHitCount;
                }
                throw new UndefinedPropertyError("BoxalinoAPI Logical Branch switch.");
            } catch(UndefinedPropertyError $exception)
            {
                foreach($this->getBlocks() as $block)
                {
                    try{
                        $object = $this->findObjectWithProperty($block, "productsCollection");
                        if(is_null($object))
                        {
                            return 0;
                        }

                        return $object->getProductsCollection()->getTotalHitCount();
                    } catch (\Exception $exception)
                    {
                        $this->logger->info($exception->getMessage());
                        continue;
                    }
                }
            }

            return 0;
        } catch(\Exception $exception)
        {
            return 0;
        }

        return 0;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        try{
            $index = 0;
            if(property_exists($this->get()->advanced->$index, ResponseDefinitionInterface::BOXALINO_PARAMETER_REDIRECT_URL))
            {
                return $this->get()->advanced->$index->redirect_url;
            }

            return null;
        } catch(\Exception $exception)
        {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isCorrectedSearchQuery() : bool
    {
        try{
            if(property_exists($this->get()->system, ResponseDefinitionInterface::BOXALINO_PARAMETER_CORRECTED_SEARCH_QUERY))
            {
                return (bool) $this->get()->system->correctedSearchQuery;
            }

            return false;
        } catch(\Exception $exception)
        {
            return false;
        }
    }

    /**
     * @return string | null
     */
    public function getCorrectedSearchQuery()
    {
        try{
            if(property_exists($this->get()->system, ResponseDefinitionInterface::BOXALINO_PARAMETER_CORRECTED_SEARCH_QUERY))
            {
                return $this->get()->system->correctedSearchQuery;
            }

            return null;
        } catch(\Exception $exception)
        {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function hasSearchSubPhrases() : bool
    {
        try{
            if(property_exists($this->get()->system, ResponseDefinitionInterface::BOXALINO_PARAMETER_HAS_SEARCH_SUBPHRASES))
            {
                return (bool) $this->get()->system->hasSearchSubPhrases;
            }

            return false;
        } catch(\Exception $exception)
        {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getRequestId() : string
    {
        try{
            $index = 0;
            if(property_exists($this->get()->advanced->$index, ResponseDefinitionInterface::BOXALINO_PARAMETER_BX_REQUEST_ID))
            {
                return $this->get()->advanced->$index->_bx_request_id;
            }

            return "N/A";
        } catch(\Exception $exception)
        {
            return "N/A";
        }
    }

    /**
     * @return string
     */
    public function getGroupBy() : string
    {
        try{
            $index = 0;
            if(property_exists($this->get()->advanced->$index, ResponseDefinitionInterface::BOXALINO_PARAMETER_BX_GROUP_BY))
            {
                return $this->get()->advanced->$index->_bx_group_by;
            }

            return "N/A";
        } catch(\Exception $exception)
        {
            return "N/A";
        }
    }

    /**
     * @return string
     */
    public function getVariantId() : string
    {
        try{
            $index = 0;
            if(property_exists($this->get()->advanced->$index, ResponseDefinitionInterface::BOXALINO_PARAMETER_BX_VARIANT_UUID))
            {
                return $this->get()->advanced->$index->_bx_variant_uuid;
            }

            return "N/A";
        } catch(\Exception $exception)
        {
            return "N/A";
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getBlocks() : \ArrayIterator
    {
        if(is_null($this->blocks))
        {
            $this->blocks = $this->getContentByType("blocks");
        }

        return $this->blocks;
    }

    /**
     * @param string $type
     * @return \ArrayIterator
     */
    public function getContentByType(string $type) : \ArrayIterator
    {
        $content = new \ArrayIterator();
        try{
            if(property_exists($this->get(), $type))
            {
                $blocks = $this->get()->$type;
                foreach($blocks as $block)
                {
                    $content->append($this->getBlockObject($block));
                }
            }
        } catch (\ErrorException $error)
        {
            /** there is no layout position for the narrative, not an issue */
        } catch (\Exception $error)
        {
            $this->logger->warning("BoxalinoResponseAPI: Something went wrong during content extract for $type: " . $error->getMessage());
        }

        return $content;
    }

    /**
     * @param \StdClass $block
     * @return AccessorInterface
     */
    public function getBlockObject(\StdClass $block) : AccessorInterface
    {
        return $this->toObject($block, $this->getAccessorHandler()->getAccessor("blocks"));
    }

    /**
     * Debug and performance information
     *
     * @return array
     */
    public function getAdvanced() : array
    {
        try{
            $index = 0;
            return array_merge($this->get()->performance, $this->get()->advanced->$index);
        } catch(\Exception $exception)
        {
            return $this->get()->performance;
        }
    }

    /**
     * @return \StdClass|null
     */
    public function get()
    {
        if(is_null($this->data))
        {
            $this->data = json_decode($this->json);
        }

        return $this->data;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->setJson($response->getBody()->getContents());

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse() : Response
    {
        return $this->response;
    }

    /**
     * @param string $json
     * @return $this
     */
    public function setJson(string $json)
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return string
     */
    public function getJson() : string
    {
        return $this->json;
    }

    /**
     * @return AccessorHandlerInterface
     */
    public function getAccessorHandler(): AccessorHandlerInterface
    {
        return $this->accessorHandler;
    }

    /**
     * @param array $segments
     * @return $this
     */
    public function addResponseSegments(array $segments)
    {
        foreach($segments as $segment)
        {
            if(isset($this->segments[$segment]))
            {
                continue;
            }

            $this->segments[] = $segment;
        }

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getResponseSegments() : array
    {
        return $this->segments;
    }
}
