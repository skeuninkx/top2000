<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Edition
 *
 * @ORM\Entity
 * @ORM\Table(name="edition")
 */
class Edition extends BaseEntity
{
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Position", mappedBy="edition", cascade={"persist"})
     * @ORM\OrderBy({"number" = "ASC"})
     *
     * @var ArrayCollection|Position[]
     */
    private $positions;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->positions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return self
     */
    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return Position[]|ArrayCollection
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param Position[]|ArrayCollection $positions
     * @return self
     */
    public function setPositions($positions): self
    {
        $this->positions = $positions;
        return $this;
    }

    /**
     * @param Position $position
     * @return self
     */
    public function addPosition(Position $position)
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->setEdition($this);
        }

        return $this;
    }

    /**
     * @param Position $position
     * @return self
     */
    public function removePosition(Position $position)
    {
        if ($this->positions->contains($position)) {
            $this->positions->removeElement($position);
            $position->setEdition(null);
        }

        return $this;
    }
}