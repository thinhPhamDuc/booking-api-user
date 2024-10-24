<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Aws\S3\S3UriParser;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\Constant;

trait S3Storage
{
    /**
     * Get temporary url for S3 file
     * @author Sakina Maezawa
     * @param string $s3Url
     * @return string signed url for S3 file
     */
    public function getTemporaryUrl(string $s3Url): string
    {
        $s3uri = new S3UriParser();
        $ret = $s3uri->parse($s3Url);

        if (!Storage::exists($ret['key'])) {
            $exception = new Exception('File not found in S3 key: ' . $ret['key']);
            Log::error($exception);
            throw $exception;
        }

        return Storage::temporaryUrl($ret['key'], now()->addMinute());
    }

    /**
     * Store file
     *
     * @param $file
     * @param null $path
     * @param string $type
     * @param bool $private
     * @return string
     */
    public function uploadFile($file, $path = null)
    {
        $fileUploadName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //convert file name to safe characters UTF8
        $fileUploadName = $this->convertSafeCharactersUtf8($fileUploadName);
        $fileName = preg_replace('/\s+/', '_', time() . '_' . $fileUploadName . '.' . $file->getClientOriginalExtension());

        if (strlen($fileName) > Constant::LIMIT_STRING_LENGTH_FILE_NAME) {
            $fileName = substr($fileName, 0, Constant::LIMIT_STRING_LENGTH_FILE_NAME);
        }

        return Storage::disk(config('filesystems.default'))->putFileAS(
            $path,
            $file,
            $fileName
        );
    }


    public function uploadFileFromPath($filePath, $path = null)
    {
        if (file_exists($filePath)) {
            $fileUploadName = pathinfo($filePath, PATHINFO_FILENAME);
            $fileUploadName = $this->convertSafeCharactersUtf8($fileUploadName);
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

            $fileName = preg_replace('/\s+/', '_', time() . '_' . $fileUploadName . '.' . $fileExtension);

            if (strlen($fileName) > Constant::LIMIT_STRING_LENGTH_FILE_NAME) {
                $fileName = substr($fileName, 0, Constant::LIMIT_STRING_LENGTH_FILE_NAME);
            }

            return Storage::disk(config('filesystems.default'))->putFileAs(
                $path,
                new File($filePath),
                $fileName
            );
        } 
    }
    /**
     * Get presigned url of S3.
     *
     * @param string $url
     * @param string $objectName
     * @return string
     */
    public function getPresignedUrl(string $url, string $objectName): string
    {
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
        ]);
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => substr($url, strpos($url, $objectName), strlen($url)),
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+1 minutes');
        // Get the actual presigned-url
        return (string) $request->getUri();
    }

    /**
     * convert string to safe characters UTF8
     *
     * @param  mixed $string
     * @return void
     */
    public function convertSafeCharactersUtf8($string)
    {
        $string = str_replace(' ', '_', $string);
        $string = preg_replace("/[^A-Za-z0-9!\-_.*()']/", '', $string);
        $string = $string ? $string : "file";
        return $string;
    }

    /**
     * Delete file
     *
     * @param $file
     * @return string
     */
    public function deleteFile($file)
    {
        return Storage::disk(config('filesystems.default'))->delete($file);
    }
}
