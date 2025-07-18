<?php
namespace App\Service;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
 {
   
    public const TOURNAMENT = '1';
    public const GAME = '2';

    public function __construct(
        private string $tournamentDirectory, 
        private string $gameDirectory, 
        private SluggerInterface $slugger,
        private Filesystem $filesystem,
    )
    { }
    public function upload(LoggerInterface $logger,UploadedFile $file, string $fileType): string
    {

    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    try {
        switch ($fileType) {
            case self::TOURNAMENT:
                // logic to determine if the user can EDIT
                // return true or false            
                $file->move($this->tournamentDirectory, $newFilename);

                break;

            case self::GAME:
                // logic to determine if the user can VIEW
                // return true or false
                $file->move($this->gameDirectory, $newFilename);

                break;
        }
            }
            catch (FileException $e) {
            $logger->error($e->getMessage());
            }
    return $newFilename;
    }

    public function remove(string $filename, string $fileType): void
    {
        switch ($fileType) {
            case self::TOURNAMENT:
                // logic to determine if the user can EDIT
                // return true or false            
                $this->filesystem->remove($this->tournamentDirectory . DIRECTORY_SEPARATOR . $filename);

                break;

            case self::GAME:
                // logic to determine if the user can VIEW
                // return true or false
                $this->filesystem->remove($this->gameDirectory . DIRECTORY_SEPARATOR . $filename);

                break;
        }
 
    }
}