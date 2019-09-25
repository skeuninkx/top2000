<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Position
 *
 * @ORM\Entity
 * @ORM\Table(name="position")
 */
class Position extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song", inversedBy="positions", cascade={"persist"})
     * @ORM\JoinColumn(name="song_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Song|null
     */
    private $song;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition", inversedBy="positions", cascade={"persist"})
     * @ORM\JoinColumn(name="edition_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Edition|null
     */
    private $edition;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $number;

    /**
     * @return Song|null
     */
    public function getSong(): Song
    {
        return $this->song;
    }

    /**
     * @param Song|null $song
     * @return self
     */
    public function setSong(Song $song = null): self
    {
        $this->song = $song;
        return $this;
    }

    /**
     * @return Edition|null
     */
    public function getEdition(): Edition
    {
        return $this->edition;
    }

    /**
     * @param Edition|null $edition
     * @return self
     */
    public function setEdition(Edition $edition = null): self
    {
        $this->edition = $edition;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return self
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;
        return $this;
    }
}