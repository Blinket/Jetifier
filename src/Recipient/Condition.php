<?php
/**
 * Created by PhpStorm.
 * User: pk
 * Date: 10.12.2017
 * Time: 12:47
 */

namespace Jetifier\Recipient;


use Jetifier\Exceptions\BadRecipientIdentifierException;

class Condition implements RecipientInterface
{

    private static $maxTopicsCount = 3;
    private $topicsCount = 0;
    private $condition = '';


    /**
     * Condition constructor.
     * @throws \Jetifier\Exceptions\BadRecipientIdentifierException
     */
    public function __construct(Topic $topic)
    {
        $this->appendTopicToCondition('', $topic->getTopic());
        $this->incrementTopicsCounter(1);
    }

    private function appendTopicToCondition(string $operator, string $topic)
    {
        $this->condition .= " $operator '$topic' in topics";
    }

    private function incrementTopicsCounter(int $amount)
    {
        $this->topicsCount += $amount;
    }

    public function addAndTopic(Topic $topic)
    {
        $this->appendTopicToCondition('&&', $topic->getTopic());
        $this->incrementTopicsCounter(1);
    }

    public function addOrTopic(Topic $topic)
    {
        $this->appendTopicToCondition('||', $topic->getTopic());
        $this->incrementTopicsCounter(1);
    }

    public function addAndCondition(Condition $subCondition)
    {
        $this->appendCondition('&&', $subCondition->getIdentifier());
        $this->incrementTopicsCounter($subCondition->topicsCount);
    }

    private function appendCondition(string $operator, string $condition)
    {
        $this->condition .= " $operator ($condition)";
    }

    /**
     * @return string
     * @throws BadRecipientIdentifierException
     */
    public function getIdentifier(): string
    {
        $this->checkTopicsCount();
        return trim($this->condition);
    }

    /**
     * @throws BadRecipientIdentifierException
     */
    private function checkTopicsCount()
    {
        if ($this->topicsCount > self::$maxTopicsCount) {
            throw new BadRecipientIdentifierException('Too many topics in condition');
        }
    }

    public function addOrCondition(Condition $subCondition)
    {
        $this->appendCondition('||', $subCondition->getIdentifier());
        $this->incrementTopicsCounter($subCondition->topicsCount);
    }
}