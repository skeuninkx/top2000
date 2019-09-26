<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Artist;
use App\Entity\Edition;
use App\Entity\Position;
use App\Entity\Song;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ImportDataCommand
 */
class ImportDataCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Edition[]
     */
    private $editions;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     * @param KernelInterface $kernel
     */
    public function __construct(ObjectManager $om, KernelInterface $kernel)
    {
        parent::__construct('app:import:data');

        $this->om = $om;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('app:import:data')
            ->setDescription('Import all Top 2000 data')
            ->setHelp('Import all Top 2000 data')
            ->addArgument('file', InputArgument::REQUIRED, 'Name the file, located in the import-folder');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '256M');

        $path = sprintf('%s/%s', $this->kernel->getProjectDir() . '/import', $input->getArgument('file'));

        if (file_exists($path) && ($spreadsheet = $this->getSpreadsheet($path))) {
            $editionsCreated = false;
            $counter = 0;

            try {
                $worksheet = $spreadsheet->setActiveSheetIndex(0);
            } catch (\Exception $e) {
                $output->writeln('Worksheet could not be loaded!');
                exit;
            }

            foreach ($worksheet->toArray() as $key => $row) {
                if ($key === 0 && !$editionsCreated) {
                    $this->setupEditions($row);
                    $editionsCreated = true;
                    continue;
                }

                $row = array_map('trim', $row);

                if ($song = $this->setupSong($row)) {
                    ++$counter;

                    if (($counter % 100) === 0) {
                        $this->om->flush();
                        $this->om->clear();

                        $counter = 0;
                    }
                } else {
                    $output->writeln(sprintf('Something went wrong during import %s - %s', $row[0], $row[1]));
                }
            }
        } else {
            throw new FileNotFoundException(null, 0, null, $path);
        }

        // Flush entities, which will not be added to batch
        $this->om->flush();
        $this->om->clear();
    }

    /**
     * @param string $path
     * @return Spreadsheet|null
     */
    private function getSpreadsheet(string $path)
    {
        try {
            $reader = (new Csv())
                ->setInputEncoding('UTF-8')
                ->setDelimiter(';')
                ->setEnclosure(0)
                ->setSheetIndex(0)
                ->setReadDataOnly(true);

            return $reader->load($path);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param array $row
     * @return bool
     */
    private function setupEditions(array $row): bool
    {
        for ($i = 4; $i < count($row); $i++) {
            $year = intval($row[$i]);

            if ($year > 0) {
                if (!$edition = $this->om->getRepository(Edition::class)->findOneByYear($year)) {
                    $edition = (new Edition())
                        ->setYear(intval($year))
                        ->setDescription(sprintf('Dit is de Top 2000 uit het jaar %d', $year));
                    $this->om->persist($edition);
                }

                // Setup cached list of all available editions
                $this->editions[$i] = $edition;
            }
        }

        try {
            $this->om->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $row
     * @return Song|bool
     */
    private function setupSong(array $row)
    {
        $artist = $this->getArtist($row[0]);
        $song = $this->getSong($artist, $row[1], intval($row[2]));

        for ($i = 4; $i < count($row); $i++) {
            $number = intval($row[$i]);

            if ($number <= 2000 && array_key_exists($i, $this->editions)) {
                $position = (new Position())
                    ->setEdition($this->editions[$i])
                    ->setSong($song)
                    ->setNumber($number);

                $song->addPosition($position);
            }
        }

        try {
            $this->om->persist($song);

            return $song;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $name
     * @return Artist
     */
    private function getArtist(string $name): Artist
    {
        if (!$artist = $this->om->getRepository(Artist::class)->findOneByName($name)) {
            $artist = (new Artist())
                ->setName($name);

            // Save artist data always, because we won't get duplicate items
            $this->om->persist($artist);
            $this->om->flush($artist);
        }

        return $artist;
    }

    /**
     * @param Artist $artist
     * @param string $title
     * @param int $releaseYear
     * @return Song
     */
    private function getSong(Artist $artist, string $title, int $releaseYear): Song
    {
        if (!$song = $this->om->getRepository(Song::class)->findOneBy(['artist' => $artist, 'name' => $title])) {
            $song = (new Song())
                ->setName($title)
                ->setArtist($artist)
                ->setReleased($releaseYear);
        }

        return $song;
    }
}
