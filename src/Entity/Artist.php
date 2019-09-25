<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Artist
 *
 * @ORM\Entity
 * @ORM\Table(name="artist")
 */
class Artist extends BaseEntity
{
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
     * @ORM\OneToMany(targetEntity="App\Entity\Song", mappedBy="artist")
     *
     * @var ArrayCollection|Song[]
     */
    private $songs;

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
     * @return Song[]|ArrayCollection
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @param Song[]|ArrayCollection $songs
     * @return self
     */
    public function setSongs($songs): self
    {
        $this->songs = $songs;
        return $this;
    }

    /**
     * Adder for songs
     *
     * @param Song $song
     * @return self
     */
    public function addSong(Song $song): self
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setArtist($this);
        }

        return $this;
    }

    /**
     * Remover for songs
     *
     * @param Song $song
     * @return self
     */
    public function removeSong(Song $song): self
    {
        if ($this->songs->contains($song)) {
            $this->songs->removeElement($song);
            $song->setArtist(null);
        }

        return $this;
    }
}
