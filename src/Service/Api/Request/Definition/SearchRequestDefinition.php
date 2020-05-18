<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Definition;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinition;

/**
 * Boxalino API Search Request definition object
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api
 */
class SearchRequestDefinition extends RequestDefinition
    implements SearchRequestDefinitionInterface
{
    /**
     * @var int
     */
    protected $maxSubPhrases = 1;

    /**
     * @var int
     */
    protected $maxSubPhrasesHits = 1;

    /**
     * @return int
     */
    public function getMaxSubPhrases(): int
    {
        return $this->maxSubPhrases;
    }

    /**
     * @param int $maxSubPhrases
     * @return SearchRequestDefinition
     */
    public function setMaxSubPhrases(int $maxSubPhrases): SearchRequestDefinition
    {
        $this->maxSubPhrases = $maxSubPhrases;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxSubPhrasesHits(): int
    {
        return $this->maxSubPhrasesHits;
    }

    /**
     * @param int $maxSubPhrasesHits
     * @return SearchRequestDefinition
     */
    public function setMaxSubPhrasesHits(int $maxSubPhrasesHits): SearchRequestDefinition
    {
        $this->maxSubPhrasesHits = $maxSubPhrasesHits;
        return $this;
    }

}
