<?php
/**
 * Stores data pertaining to a file.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_File extends Application_Model_Abstract {
    /**
     * The file's binary data (as string output from {@link PHP_MANUAL#file_get_contents()}).
     * 
     * @var string
     */
    protected $fileData = '';

    /**
     * The file's filename.
     * 
     * @var string
     */
    protected $fileName = '';

    /**
     * The file's mime type.
     * 
     * @var string
     */
    protected $fileType = '';

    /**
     * The file's size in bytes.
     *
     * @var int
     */
    protected $fileSize = 0;

    /**
     * Set a file's data.
     * 
     * @param string $fileData
     * 
     * @return Application_Model_File
     */
    public function setFileData($fileData) {
        $this->fileData = $fileData;
        return $this;
    }

    /**
     * Get a file's data.
     *
     * @see Application_Model_File::setFileData()
     * 
     * @return string
     */
    public function getFileData() {
        return $this->fileData;
    }

    /**
     * Set a file's full name (including it's path on disk).
     * 
     * @param string $fileName
     * 
     * @return Application_Model_File
     */
    public function setFileName($fileName) {
        $this->fileName = (string) $fileName;
        return $this;
    }

    /**
     * Get a file's full name.
     *
     * @see Application_Model_File::setFileName()
     *
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * Set a file's mime type.
     *
     * @param string $fileType
     *
     * @return Application_Model_File
     */
    public function setFileType($fileType) {
        $this->fileType = (string) $fileType;
        return $this;
    }

    /**
     * Get a file's mime type.
     *
     * @see Application_Model_File::setFileType
     *
     * @return string
     */
    public function getFileType() {
        return $this->fileType;
    }

    /**
     * Set a file's size.
     *
     * @param int $fileSize In bytes.
     *
     * @return Application_Model_File
     */
    public function setFileSize($fileSize) {
        $this->fileSize = (int) $fileSize;
        return $this;
    }

    /**
     * Get a file's size.
     *
     * @see Application_Model_File::setFileSize
     * 
     * @return int In bytes.
     */
    public function getFileSize() {
        return $this->fileSize;
    }
}