<?php

namespace App\Core\Domain\Model;

use App\Domain\ValueObjects\FileId;
use DateTimeImmutable;
use App\Shared\Domain\Model\EntityInterface;
use App\Shared\Infrastructure\ValueObject\UuidValueObject;
use Doctrine\Common\Collections\Collection;
use Illuminate\Http\UploadedFile;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="attachments")
 */
class Attachment implements EntityInterface
{
    private ?string $originMd5Name;
    private ?string $newDirPath;
    private ?string $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length="255")
     */
    private ?string $path;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail_size", type="string", length="255")
     */
    private ?string $slugBySize;

    /**
     * One Attachment has many Attachments
     *
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="parent", cascade={"all"})
     */
    protected Collection $thumbnails;
    /**
     * Many Attachments have One Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment", inversedBy="thumbnails", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="uuid")
     */
    private Attachment $parent;

    private ?array $cropFilesArray;
    private UploadedFile $uploadFile;

    public function __construct(

        /**
         * @var UuidValueObject
         *
         * @ORM\Id
         * @ORM\Column(type="uuid", length="36", unique="true")
         */
        private readonly UuidValueObject $uuid,

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length="255")
         */
        private ?string $name,

        /**
         * @var string
         *
         * @ORM\Column(name="original_name", type="string", length="255")
         */
        private readonly string $originName,

        /**
         * @var string
         *
         * @ORM\Column(name="type", type="string", length="255")
         */
        private readonly string $type,

        /**
         * @var string
         *
         * @ORM\Column(name="created_at", type="datetime_immutable", length="255")
         */
        private readonly DateTimeImmutable $createdAt
    )
    {
        $this->thumbnails = new ArrayCollection();
    }

    public static function addFileName(){

    }

    /**
     * @return DateTimeImmutable
     **/
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function uploadFile(): UploadedFile
    {
        return $this->uploadFile;
    }

    public function uuid(): UuidValueObject
    {
        return $this->uuid;
    }

    public function fileType(): string
    {
        return $this->fileType;
    }

    public function extension(): string
    {
        return $this->uploadFile->getClientOriginalExtension();
    }

    public function originName(): string
    {
        return $this->originName;
    }

    public function originNameWithoutExtension(): string
    {
        return pathinfo($this->originName, PATHINFO_FILENAME);
    }

    public function originMd5Name(): string
    {
        return $this->name().".".$this->extension();
    }

    public function type(): string
    {
        return $this->type;
    }

    public function dirPath(): string
    {
        return $this->newDirPath;
    }

    public function checkDirectory(): string
    {

        return dirname($this->path());
    }

    public function tmpPath(): string
    {
        return $this->uploadFile->getPathname();
    }

    public function fileNameWithoutExtension(): string
    {
        return $this->newOriginNameWithoutExt;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function pathOriginFile(): string
    {
        return $this->path;
    }



    public function pathThumbnail(): string
    {
        return $this->dirPath() . "/" . $this->addOriginMd5Name();

    }

    public function newMd5FileName(
        string $name
    )
    {
        $this->newOriginNameWithoutExt = $name;
    }

    /**
     * @return string
     */
    public function parentId(): string
    {
        return isset($this->parent) ? $this->parent->uuid() : '0';
    }

    public function thumbnailSize(): string
    {
       return $this->slugBySize ?? "0";
    }

    public function addOriginMd5Name()
    {
        return $this->name().".".$this->extension();
    }

    public function addFileType(string $fileType){
        $this->fileType = $fileType;
    }

    public function addUploadFile(UploadedFile $file)
    {
        $this->uploadFile = $file;
    }

    public function addDirPath()
    {
        $arrayForDir = array_slice(str_split($this->name(), 2), 0, 3);
        $dir = "";

        foreach ($arrayForDir as $item) {
            $dir .= "/" . $item;
        }

        $this->newDirPath = $dir;
        $this->path = $this->dirPath() . "/" . $this->addOriginMd5Name();
    }

    public function addPathThumbnail(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function addThumbnailSize($slugBySize)
    {
        $this->slugBySize = $slugBySize;
    }

//    public function addFileNameWithoutExtension(string $fileName)
//    {
//        $this->newOriginNameWithoutExt = $fileName ?? null;
//    }

    public function addParent(
        Attachment $attachment = null
    ): void
    {
        $this->parent = $attachment ?? $this;
    }

    public function addThumbnail(Attachment $attachment){
        $this->thumbnails[] = $attachment;
    }

    public function thumbnails(): Collection
    {
        return $this->thumbnails;
    }

    private static function setTypeFile(
        object $fileObject
    ): string
    {
        $pieces = explode("/", $fileObject->getClientMimeType());
        return $pieces[0];
    }

    public function addCropFiles(
        FileId $parentFileId,
        array  $cropFile
    )
    {

        $cropFileId = FileId::create()->id();

        $this->cropFilesArray[$cropFile['slugBySize']] = [
            'uuid' => $cropFileId,
            'name' => $cropFile['filenameWithoutExc'],
            'original_name' => $this->originName(),
            'parent_id' => $parentFileId->id(),
            'path' => $cropFile['path'],
            'type' => $cropFile['type'],
            'thumbnail_size' => $cropFile['slugBySize'],
            //'created_at' => $this->createdAt()->format("Y-m-d H:i:s")
            'created_at' => $this->createdAt()->getTimestamp()
        ];
    }

    public function cropFiles(): array
    {
        return $this->cropFilesArray ?? [];
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid()->uuid(),
            'name' => $this->name(),
            'originalName' => $this->originName(),
            'parentId' => $this->parentId(),
            'path' => $this->path(),//$this->pathOriginFile(),
            'type' => $this->type(),
            'thumbnailSize'=> $this->thumbnailSize(),
            'createdAt' => $this->createdAt()->getTimestamp(),
            'thumbnails' => array_reduce($this->thumbnails->toArray(), fn($a, Attachment $e) => [...$a, $e->thumbnailSize() => $e->thumbnailToArray()], [])
            //'cropFiles' => $this->cropFilesArray ?? null
        ];
    }

    public function thumbnailToArray(): array
    {
        return [
            'uuid' => $this->uuid()->uuid(),
            'name' => $this->name(),
            'originalName' => $this->originName(),
            'parentId' => $this->parentId(),
            'path' => $this->path(),
            'type' => $this->type(),
            'thumbnailSize'=> $this->thumbnailSize(),
            'createdAt' => $this->createdAt()->getTimestamp()
        ];
    }
}
