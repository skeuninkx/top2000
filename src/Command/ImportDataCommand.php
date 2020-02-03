<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Artist;
use App\Entity\Edition;
use App\Entity\Position;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ImportDataCommand
 *
 * @author Sjors Keuninkx <sjors.keuninkx@gmail.com>
 */
class ImportDataCommand extends Command
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel, EntityManagerInterface $em)
    {
        parent::__construct('app:import:data');

        $this->kernel = $kernel;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Import Top 2000 data')
            ->setHelp('Import Top 2000 data')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Import all existing years')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if ($input->getOption('all')) {
            foreach ($this->getYears() as $year) {
                $this->importYear(intval($year));
            }
        } else {
            $question = new ChoiceQuestion('Which year needs to be imported?', $this->getYears());
            $answer = $helper->ask($input, $output, $question);

            $this->importYear(intval($answer));
        }
    }

    /**
     * @return array
     */
    private function getYears(): array
    {
        $years = [];

        $finder = new Finder();
        $finder->files()->in(sprintf('%s/%s', $this->kernel->getProjectDir(), 'import'));

        foreach ($finder as $file) {
            if (preg_match('/^TOP-2000-(\d{4}).xlsx$/i', $file->getFilename(), $matches)) {
                $years[] = $matches[1];
            }
        }

        $years = array_unique($years);
        sort($years);

        return $years;
    }

    /**
     * @param int $year
     * @return bool
     */
    private function importYear(int $year): bool
    {
        $path = sprintf('%s/%s/%s', $this->kernel->getProjectDir(), 'import', sprintf('TOP-2000-%d.xlsx', $year));

        if ($spreadSheet = $this->getSpreadSheet($path)) {
            try {
                $first = true;
                $edition = null;

                $sheet = $spreadSheet->setActiveSheetIndex(0);

                foreach ($sheet->toArray() as $item) {
                    if ($first === true) {
                        $edition = (new Edition())
                            ->setYear(intval($item[0]))
                            ->setDescription(sprintf('Dit is de Top 2000 van %d', $item[0]));
                        $this->em->persist($edition);

                        $first = false;

                        continue;
                    }

                    $artist = $this->getArtist($item[2]);
                    $song = $this->getSong($artist, $item[1], intval($item[3]));

                    // Setup position
                    $position = (new Position())
                        ->setEdition($edition)
                        ->setSong($song)
                        ->setNumber(intval($item[0]));

                    $this->em->persist($position);
                    $this->em->flush();
                }

                return true;
            } catch (PhpSpreadsheet\Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param string $path
     * @return Spreadsheet
     */
    private function getSpreadSheet(string $path): Spreadsheet
    {
        try {
            $reader = (new Xlsx())
                ->setReadDataOnly(true);

            return $reader->load($path);
        } catch (Reader\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $name
     * @return Artist
     */
    private function getArtist(string $name): Artist
    {
        if (!$artist = $this->em->getRepository(Artist::class)->findOneBy(['name' => $name])) {
            $artist = (new Artist())
                ->setName($name);
        }

        return $artist;
    }

    /**
     * @param Artist $artist
     * @param string $name
     * @param int $released
     * @return Song
     */
    private function getSong(Artist $artist, string $name, int $released): Song
    {
        if (!$song = $this->em->getRepository(Song::class)->findOneBy(['name' => $name, 'artist' => $artist])) {
            $song = (new Song())
                ->setName($name)
                ->setReleased($released)
                ->setArtist($artist);
        }

        return $song;
    }
}
