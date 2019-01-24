<?php

/**
 * TODO: Filter regexp, keep releases, destinations/servers
 */

/**
 * Class FTPDeploy
 */
class FTPDeploy
{
   # Local deploy source dir
   private $sourceDir;

   # Remote target dir
   private $targetDir;

   # Remote release dir
   private $releaseDir;

   # Remote deploy root dir
   private $deployRoot;

   # Local files to be deployed
   private $localFileList = [];

   # FTP connection handle
   private $connection;

   # Use deploy mode (create releases)
   private $deployMode = false;

   /**
    * FTPDeploy constructor.
    * @param string $sourceDir
    * @param string $targetDir
    * @param $ftpConfig
    */
   public function __construct(string $sourceDir, string $targetDir, $ftpConfig)
   {
      $ftpConfig = (object) $ftpConfig;

      if (is_string($sourceDir)) {
         $this->sourceDir = $sourceDir;
         if (!is_dir($sourceDir)) {
            die('sourceDir is not a valid directory path' . PHP_EOL);
         }
      } else {
         die('Invalid argument for sourceDir');
      }

      if (is_string($targetDir)) {
         $this->targetDir = $targetDir;
         $this->deployRoot = $targetDir;
      } else {
         die('Invalid argument for targetDir' . PHP_EOL);
      }

      if (isset($ftpConfig) && isset($ftpConfig->host)) {
         $host = $ftpConfig->host;
         $port = $ftpConfig->port ? $ftpConfig->port : null;
         $username = $ftpConfig->username ? $ftpConfig->username : '';
         $password = $ftpConfig->password ? $ftpConfig->password : '';
         $this->deployMode = $ftpConfig->deployMode ? $ftpConfig->deployMode : false;

         $this->connection = ftp_connect($host, $port ? $port : 21);
         ftp_login($this->connection, $username, $password);
         ftp_pasv($this->connection, true);
      } else {
         die('Missing ftp config parameters' . PHP_EOL);
      }
   }

   /**
    * Get files in array
    * @param string $dir
    * @return void
    */
   private function updateLocalFileList(string $dir): void
   {
      if (is_dir($dir)) {
         $dirContent = scandir($dir);
         array_shift($dirContent); // remove .
         array_shift($dirContent); // remove ..

         foreach ($dirContent as $value) {
            $this->updateLocalFileList($dir . '/' . $value);
         }
      } elseif (is_file($dir)) {
         array_push($this->localFileList, $dir);
      }
   }

   /**
    * Returns tuples of [source, target] for file list
    * @param array $fileList
    * @return array
    */
   private function getFileUploadPaths(array $fileList): array
   {
      $tuples = [];

      foreach ($fileList as $value) {
         $source = $value;
         $target = null;

         $target = str_replace($this->sourceDir, $this->targetDir, $source);

         array_push($tuples, [$source, $target]);
      }

      return $tuples;
   }

   /**
    * Create target dirs recursively
    * @param $subDir
    */
   private function ftp_mkSubDirs($subDir): void
   {
      $currentDir = ftp_pwd($this->connection);
      $relativeSubDir = str_replace($this->targetDir, '', $subDir);
      $parts = explode('/', $relativeSubDir);
      foreach ($parts as $part) {
         if ($part === '') {
            continue;
         }
         if (!@ftp_chdir($this->connection, $part)) {
            ftp_mkdir($this->connection, $part);
            ftp_chdir($this->connection, $part);
         }
      }
      ftp_chdir($this->connection, $currentDir);
   }

   /**
    * Upload file
    * @param array $pathTuple
    * @return bool
    */
   private function uploadFile(array $pathTuple): bool
   {
      $source = $pathTuple[0];
      $target = $pathTuple[1];

      $targetParts = explode('/', $target);
      array_pop($targetParts);

      // Create recursive dir
      $this->ftp_mkSubDirs(implode('/', $targetParts));

      $success = ftp_put($this->connection, $target, $source, FTP_BINARY);
      return $success;
   }

   /**
    * Upload array of path tuples
    * @param array $uploadPaths
    */
   private function uploadFiles(array $uploadPaths): void
   {
      foreach ($uploadPaths as $tuple) {
         $success = $this->uploadFile($tuple);
         if (!$success) {
            error_log('>>> ERROR: Failed to upload file: ' . $tuple[0] . '   ==>   ' . $tuple[1]);
         } else {
            error_log('>>> UPLOADED: ' . $tuple[0] . '   ==>   ' . $tuple[1]);
         }
      }
   }

   /**
    * If deploy mode:
    * Create release dir
    */
   private function createDeployDir(): void
   {
      if ($this->deployMode === true) {
         ftp_chdir($this->connection, $this->targetDir);
         $releases = 'releases';

         if (!@ftp_chdir($this->connection, $releases)) {
            ftp_mkdir($this->connection, $releases);
            ftp_chdir($this->connection, $releases);
         }

         $this->releaseDir = time() . '';
         if (!@ftp_chdir($this->connection, $this->releaseDir)) {
            ftp_mkdir($this->connection, $this->releaseDir);
            ftp_chdir($this->connection, $this->releaseDir);

            // Rewrite target dir to release dir
            $this->targetDir = $this->targetDir . '/releases/' . $this->releaseDir;
         } else {
            die('UNEXPECTED ERROR: Release dir already exists! Maybe try again.' . PHP_EOL);
         }
      }
   }

   /**
    * If deploy mode:
    * Link to current release via symlink
    * or redirect with .htaccess
    */
   private function updateSymlink(): void
   {
      if ($this->deployMode === true) {
         $command = 'ln -s ' . $this->targetDir . ' current';

         ftp_chdir($this->connection, $this->deployRoot);

         @ftp_delete($this->connection, $this->deployRoot . '/current');

         if (ftp_exec($this->connection, $command)) {
            echo '>>> Symlink "current" updated.' . PHP_EOL;
         } else {
            echo '>>> ERROR: Failed to update symlink "current"' . PHP_EOL;
            echo '>>> Creating .htaccess link instead' . PHP_EOL;

            // Use .htaccess instead
            $temp = tmpfile();
            $htaccess = "
               RewriteEngine on
               RewriteRule ^(.*)$ /releases/$this->releaseDir%{REQUEST_URI} [L,NC]
            ";
            fwrite($temp, $htaccess);
            rewind($temp);
            ftp_fput($this->connection, $this->deployRoot . '/.htaccess', $temp, FTP_BINARY);
            fclose($temp);
         }
      }
   }

   /**
    * Check if specified target upload dir does exist
    */
   private function checkTargetUploadDir(): void
   {
      if (!@ftp_chdir($this->connection, $this->targetDir)) {
         die('>>> ERROR: Upload dir does not exist!' . PHP_EOL);
      }
   }

   /**
    * Start deploy procedure
    */
   public function deploy(): void
   {
      $this->checkTargetUploadDir();
      $this->createDeployDir();
      $this->updateLocalFileList($this->sourceDir);
      $this->uploadFiles($this->getFileUploadPaths($this->localFileList));
      $this->updateSymlink();
   }
}
