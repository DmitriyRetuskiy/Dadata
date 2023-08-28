<?php

namespace App\Core\Application\Service;

use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickPixel;

class MaskService extends Imagick
{

    /** Radius for blurImage  @var int */
    private const RADIUS = 3;
    /** Sigma for blurImage  @var int */
    private const SIGMA = 3;
    /** Files directory @var string */
    public static string $dir;
    /** Array paths mask @var array */
    public static array $images;
    /** Name path base @var string */
    public static string $base;
    /** Colors for mask  @var array */
    public static array $colors;
    /** Mix methods @var array */
    public static array $methods;

    /** Matching photoshop methods in PHP  @var array */
    public array $photoshopMethods = [
        "Normal" => Imagick::COMPOSITE_NO,
        "Dissolve" => Imagick::COMPOSITE_DISSOLVE,
        "Darken" => Imagick::COMPOSITE_DARKEN,
        "Multiply" => Imagick::COMPOSITE_MULTIPLY,
        "Color Burn" => Imagick::COMPOSITE_COLORBURN,
        "Linear Burn" => Imagick::COMPOSITE_LINEARBURN,
        "Darker Color" => Imagick::COMPOSITE_DARKEN,
        "Lighten" => Imagick::COMPOSITE_LIGHTEN,
        "Screen" => Imagick::COMPOSITE_SCREEN,
        "Color Dodge" => Imagick::COMPOSITE_COLORDODGE,
        "Linear Dodge (Add)" => Imagick::COMPOSITE_LINEARDODGE,
        "Lighter Color" => Imagick::COMPOSITE_LIGHTEN,
        "Overlay" => Imagick::COMPOSITE_OVERLAY,
        "Solf Light" => Imagick::COMPOSITE_SOFTLIGHT,
        "Hard Light" => Imagick::COMPOSITE_HARDLIGHT,
        "Vivid Light" => Imagick::COMPOSITE_VIVIDLIGHT,
        "Linear Light" => Imagick::COMPOSITE_LINEARLIGHT,
        "Pin Light" => Imagick::COMPOSITE_PINLIGHT,
        "Difference" => Imagick::COMPOSITE_DIFFERENCE,
        "Exclusion" => Imagick::COMPOSITE_EXCLUSION,
        "Subtract" => Imagick::COMPOSITE_SATURATE,
        "Hue" => Imagick::COMPOSITE_HUE,
        "Saturation" => Imagick::COMPOSITE_SATURATE,
        "Color" => Imagick::COMPOSITE_COLORIZE,
        "Luminosity" => Imagick::COMPOSITE_LUMINIZE

    ];

    /**
     * Release mask method
     *
     * @param object $command
     * @return string
     * @throws \ImagickException
     */

    public function releaseImageFromMask(object $command): string
    {
        $this->putMaskFilesToStorage([
            $command->fileBase->getClientOriginalName() => $command->fileBase,
            $command->fileMask1->getClientOriginalName() => $command->fileMask1,
            $command->fileMask2->getClientOriginalName() => $command->fileMask2,
            $command->fileReflection->getClientOriginalName() => $command->fileReflection
        ]);

        // после создания public Storage получить путь к нему
        self::$methods = [$command->nameMethod1, $command->nameMethod2, 'Screen'];
        $this->changeArrayMethods();
        self::$base = $command->fileBase->getClientOriginalName();
        self::$images = [$command->fileMask1->getClientOriginalName(), $command->fileMask2->getClientOriginalName(), $command->fileReflection->getClientOriginalName()];
        self::$colors = [$command->colorMask1, $command->colorMask2, ""]; //   "#e4acb3","#4f332b"

        $diskPath = Storage::path('');
        $imgPath = $diskPath . 'result_image.jpg';

//        $this->dir = $diskPath; //   C:/OpenServer/domains/git/is-media-api-lumen/storage/app/public/ /var/www/html/storage/app/public/
        self::$dir = $diskPath;
        $this->createPath();
        $this->createImage(); // можно заменить на  createImageThreshold(faster)/createImage(slower)
        $this->setImageFormat('jpeg');
        $this->writeImage($imgPath);

        return $imgPath;
    }

    /**
     * Add files in Storage
     *
     * @param array $files
     * @return void
     */

    public function putMaskFilesToStorage(array $files): void
    {
        foreach ($files as $name => $obj) {
            Storage::disk('public')->putFileAs("", $obj, $name);
        }
    }

    /**
     * Change methods from photoshop to PHP
     *
     * @return void
     */

    public function changeArrayMethods(): void
    {
        foreach (self::$methods as $key => $method) {
            foreach ($this->photoshopMethods as $keyPh => $methodPh) {
                if ($method == $keyPh) {
                    self::$methods[$key] = $methodPh;
                }
            }
        }
    }

    /**
     * Create path for masks
     *
     * @return void
     */

    public function createPath(): void
    {
//        $this->base = $this->dir . $this->base;     // путь для базы
//        foreach ($this->images as $i => $image) {   // пути для масок
//            $this->images[$i] = $this->dir . $image;
//        }

        self::$base = self::$dir . self::$base;     // путь для базы
        foreach (self::$images as $i => $image) {   // пути для масок
            self::$images[$i] = self::$dir . $image;
        }
    }

    /**
     * Create image use mask array, methods array
     *
     * @return void
     * @throws \ImagickException
     * @throws \ImagickPixelException
     */

    public function createImage(): void
    {
        $base = new Imagick(self::$base);
//        $start = microtime(true);
        //--------------Алгоритм маски реверс от белго рабочее----------------------
        foreach (self::$images as $i => $imagPath) {
            $mask = new Imagick(self::$images[$i]);

            if (self::$colors[$i] != "") {
                $clut = new Imagick();
                //--отражаем размытие нужно для Multiply && Linear Burn;
                if (self::$methods[$i] == Imagick::COMPOSITE_MULTIPLY || self::$methods[$i] == Imagick::COMPOSITE_LINEARBURN) {
                    $clut->newImage(1500, 1500, new ImagickPixel('white'));       // заливаем
                    $mask->compositeImage($clut, Imagick::COMPOSITE_MULTIPLY, 0, 0,);
                    $fuzz = 0.1;
                    $max = $mask->getQuantumRange();
                    $max = $max["quantumRangeLong"];
                    $mask->transparentPaintImage($mask->getImagePixelColor(0, 0), 0, $fuzz * $max, true);
                }

                //---------------
                $clut->newImage(1500, 1500, new ImagickPixel(self::$colors[$i]));       // заливаем
                $mask->compositeImage($clut, Imagick::COMPOSITE_MULTIPLY, 0, 0,);
                $fuzz = 0.1;
                $max = $mask->getQuantumRange();
                $max = $max["quantumRangeLong"];
                $mask->transparentPaintImage($mask->getImagePixelColor(0, 0), 0, $fuzz * $max, false); //
                //---нужно  для Multiply и Linear Burn
//                if (self::$methods[$i] == Imagick::COMPOSITE_MULTIPLY || self::$methods[$i] == Imagick::COMPOSITE_LINEARBURN) {
////                    $mask->adaptiveBlurImage(5, 1); // размытие
////                    $mask->gaussianBlurImage(3, 2);
//                    $mask->blurImage(self::RADIUS, self::SIGMA);
//                }

            }

            $base->compositeImage($mask, self::$methods[$i], 0, 0);
        }

        // заполнение изображения
        $this->newImage(100, 100, new ImagickPixel('green')); // transparent
        $this->setImage($base); //$base

//        var_dump(round(microtime(true) - $start, 4));

    }

    /**
     * Similar createImage method but using thresholdImage function
     *
     * @return void
     * @throws \ImagickException
     * @throws \ImagickPixelException
     */

    public function createImageThreshold(): void
    {

        $base = new Imagick(self::$base);
//        $start = microtime(true);

        foreach (self::$images as $i => $imagPath) {
            $mask = new Imagick(self::$images[$i]);
            if (self::$colors[$i] != "") {
                $clut = new Imagick();                                                            // создаем background
                //--отражаем размытие нужно для Multiply && Linear Burn;
                if (self::$methods[$i] == Imagick::COMPOSITE_MULTIPLY || self::$methods[$i] == Imagick::COMPOSITE_LINEARBURN) {
                    $max = $mask->getQuantumRange();
                    $max = $max["quantumRangeLong"];
                    $mask->thresholdImage(0.77 * $max); //0.77
                }
                //---------------
                $clut->newImage(1500, 1500, new ImagickPixel(self::$colors[$i]));       // заливаем
                $mask->compositeImage($clut, Imagick::COMPOSITE_MULTIPLY, 0, 0,);
                $fuzz = 0.1;
                $max = $mask->getQuantumRange();
                $max = $max["quantumRangeLong"];
                $mask->transparentPaintImage($mask->getImagePixelColor(0, 0), 0, $fuzz * $max, false); //
//                if (self::$methods[$i] == Imagick::COMPOSITE_MULTIPLY || self::$methods[$i] == Imagick::COMPOSITE_LINEARBURN) {
////                    $mask->adaptiveBlurImage(5, 1); // размытие
////                    $mask->gaussianBlurImage(3, 2);
//                    $mask->blurImage(self::RADIUS, self::SIGMA);
//                }
            }

            $base->compositeImage($mask, self::$methods[$i], 0, 0);

        }

        $this->newImage(100, 100, new ImagickPixel('green')); // transparent
        $this->setImage($base); //$base

        //скорость
//        var_dump(round(microtime(true) - $start, 4));
    }

}
