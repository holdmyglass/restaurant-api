<?php

namespace Modules\File\Services\V1;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\BmpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Interfaces\EncoderInterface;
use Intervention\Image\Laravel\Facades\Image;
use Modules\File\Enums\ImageFormatEnum;
use Modules\File\Enums\ImageSizeEnum;

class ImageUploadService
{
    protected $defaultFormat;

    public function __construct($defaultFormat = ImageFormatEnum::JPG)
    {
        $this->defaultFormat = $defaultFormat;
    }

    public function upload(UploadedFile $file, array $sizes, $format = null): ?string
    {
        $format = $format ?: $this->defaultFormat->value;
        $validSizes = $this->validateSizes($sizes, $file);
        $filename = null;
        $uploadedFiles = []; // Track uploaded files for cleanup

        // Generate a unique filename once
        $uniqueFilename = $this->generateUniqueFilename('original', $format);

        // Process the original image
        $imagePath = $file->getRealPath();
        $originalSizeEnum = ImageSizeEnum::from(ImageSizeEnum::ORIGINAL->value);
        $originalFolder = $originalSizeEnum->getLocation();

        // Process the original image
        $filename = $this->processOriginalImage($imagePath, $uniqueFilename, $format);
        if ($filename) {
            $uploadedFiles[] = public_path("images/$originalFolder/$filename");
        } else {
            return null; // Return null if original upload failed
        }

        // Process valid sizes
        foreach ($validSizes as $validSize) {
            $resizedFilename = $this->processResizedImage($imagePath, $validSize->value, $uniqueFilename, $format);
            if ($resizedFilename) {
                $uploadedFiles[] = public_path("images/{$validSize->getLocation()}/{$resizedFilename}");
            } else {
                $this->cleanupUploadedFiles($uploadedFiles); // Cleanup if any upload fails

                return null; // Return null if any resize upload failed
            }
        }

        return $filename; // Return the filename of the original image
    }

    private function processOriginalImage(string $imagePath, string $uniqueFilename, string $format): ?string
    {
        $originalSizeEnum = ImageSizeEnum::from(ImageSizeEnum::ORIGINAL->value);
        $originalFolder = $originalSizeEnum->getLocation();
        $destinationPath = "images/$originalFolder/$uniqueFilename";

        try {
            $originalImage = Image::read($imagePath);

            // Get the appropriate encoder based on the format
            $encoder = $this->getEncoder($format);

            // Encode the original image using the selected encoder
            $imageData = (string) $originalImage->encode($encoder);

            // Store the encoded image in the specified destination
            Storage::disk('public')->put($destinationPath, $imageData);

            return $uniqueFilename; // Return the unique filename
        } catch (\Exception $e) {
            // Handle the error, e.g., log the exception
            return null;
        }
    }

    private function processResizedImage(string $imagePath, string $size, string $uniqueFilename, string $format): ?string
    {
        $originalImage = Image::read($imagePath);
        $resolution = ImageSizeEnum::from($size)->getResolution();
        $width = $resolution[0];
        $height = $resolution[1];

        // Resize and crop the image to fit the desired dimensions
        $resizedImage = $originalImage->cover($width, $height);

        $folderName = ImageSizeEnum::from($size)->getLocation();
        $destinationPath = "images/$folderName/$uniqueFilename";

        try {
            // Encode the image to a string
            $encoder = $this->getEncoder($format);

            // Encode the resized image using the selected encoder
            $imageData = (string) $resizedImage->encode($encoder);

            // Store the image using Storage::put()
            Storage::disk('public')->put($destinationPath, $imageData);

            return $uniqueFilename; // Return the unique filename
        } catch (\Exception $e) {
            return null; // Return null if saving fails
        }
    }

    private function generateUniqueFilename(string $folder, string $format): string
    {
        do {
            $filename = Str::ulid().'.'.$format; // Generate a unique filename
            // Check if the filename exists in any folder
            $exists = false;
            foreach (ImageSizeEnum::cases() as $sizeEnum) {
                $path = public_path("images/{$sizeEnum->getLocation()}/{$filename}");
                if (file_exists($path)) {
                    $exists = true; // Set exists to true if the file is found
                    break; // Exit the loop if a match is found
                }
            }
        } while ($exists); // Repeat until a unique filename is found

        return $filename; // Return the unique filename
    }

    private function validateSizes(array $sizes, UploadedFile $file): array
    {
        $validSizes = [];
        $originalImage = Image::read($file);
        $originalWidth = $originalImage->width();
        $originalHeight = $originalImage->height();

        foreach ($sizes as $size) {
            if (ImageSizeEnum::isValid($size->value)) {

                if ($size === ImageSizeEnum::ORIGINAL) {
                    $validSizes[] = $size; // Add ORIGINAL size directly

                    continue; // Skip the rest of the loop for this size
                }

                $resolution = ImageSizeEnum::from($size->value)->getResolution();
                $width = $resolution[0];
                $height = $resolution[1];

                // Check if the requested size is valid
                if ($width <= $originalWidth && $height <= $originalHeight) {
                    $validSizes[] = $size;
                }
            }
        }

        return $validSizes;
    }

    private function cleanupUploadedFiles(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file); // Delete the file
            }
        }
    }

    private function getEncoder(string $format): EncoderInterface
    {
        switch (strtolower($format)) {
            case 'jpeg':
            case 'jpg':
                return new JpegEncoder;

            case 'png':
                return new PngEncoder;

            case 'gif':
                return new GifEncoder;

            case 'bmp':
                return new BmpEncoder;

            case 'webp':
                return new WebpEncoder;
            default:
                return new JpegEncoder;

        }
    }
}
