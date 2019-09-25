<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Song
 *
 * @ORM\Entity
 * @ORM\Table(name="song")
 */
class Song extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Artist", inversedBy="songs", cascade={"persist"})
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Artist|null
     */
    private $artist;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $released;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Position", mappedBy="song", cascade={"persist"})
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
     * @return Artist|null
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist|null $artist
     * @return self
     */
    public function setArtist(Artist $artist = null): self
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        $this->setSlug($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return self
     */
    protected function setSlug(string $slug): self
    {
        $slugify = new Slugify();

        $this->slug = $slugify->slugify($slug);
        return $this;
    }

    /**
     * @return int
     */
    public function getReleased(): int
    {
        return $this->released;
    }

    /**
     * @param int $released
     * @return self
     */
    public function setReleased(int $released): self
    {
        $this->released = $released;
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
            $position->setSong($this);
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
            $position->setSong(null);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s - %s', $this->artist->getName(), $this->name);
    }
}
