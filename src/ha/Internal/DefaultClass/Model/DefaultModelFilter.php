<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model;

/**
 * Class DefaultModelFilter
 * @property int $size
 * @property int $from
 * @property bool $computeTotalRowsCount
 */
class DefaultModelFilter extends ModelDefaultAbstract
{
    /** @var  bool */
    protected $computeTotalRowsCount = false;

    /** @var array */
    protected $filterProperties = [];

    /** @var int */
    protected $from = 0;

    /** @var int */
    protected $size = 10;

    /**
     * @return boolean
     */
    public function getComputeTotalRowsCount()
    {
        return $this->computeTotalRowsCount;
    }

    /**
     * Property 'filterProperties' getter.
     * @return array
     */
    public function getFilterProperties()
    {
        return $this->filterProperties;
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Property 'filterProperties' isset checker.
     * @return bool
     */
    public function hasFilterProperties(): bool
    {
        return (count($this->filterProperties) !== 0);
    }

    /**
     * @param boolean $computeTotalRowsCount
     *
     * @return $this
     */
    public function setComputeTotalRowsCount(bool $computeTotalRowsCount)
    {
        $this->computeTotalRowsCount = $computeTotalRowsCount;
        return $this;
    }

    /**
     * @param int $from
     *
     * @return $this
     */
    public function setFrom(int $from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize(int $size)
    {
        $this->size = $size;
        return $this;
    }

}