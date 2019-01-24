# !!! WORK IN PROGRESS !!!

# EASY FTP DEPLOY
[![npm](https://img.shields.io/npm/v/easy-ftp-deploy.svg)](https://www.npmjs.com/package/easy-ftp-deploy)
[![npm](https://img.shields.io/npm/dm/easy-ftp-deploy.svg)](https://www.npmjs.com/package/easy-ftp-deploy)

Simple deployment tool that works with almost every cheap web hosts that supports FTP.   
The tool supports two different deploy modes:
#### Upload mode:    
Just upload the files from `sourceDir` to `targetDir`.   
**Existing files will be overwritten!**

#### Release mode:  
Create a separate folder for each release and create a symlink to the current version.
If the web host does not support creating symlinks with FTP, which is common for cheap web hosts on Apache, 
an `.htaccess` file will be created that re-routes (invisibly) to the current version.

If you are on `nginx` or any other server and do not have support for creating symlinks via FTP, 
you need to manage the routing by yourself. You can get the path of the current version from the 
created `.htaccess` file.  

### Installation
`npm i -D easy-ftp-deploy`  
`yarn add --dev easy-ftp-deploy`

Alternatively you can install it globally:   
`npm i -g easy-ftp-deploy`     
`yarn global add easy-ftp-deploy`

### Usage
`npx easy-ftp-deploy`

Or if you installed it globally:  
`easy-ftp-deploy`

### Config
Create the config file `easy-ftp-deploy.js` in your project root and setup your FTP access data.
```js
module.exports = {
   host: 'myhost.com',
   port: 21,
   username: 'bill',
   password: '123',
   deployMode: true,
   sourceDir: './build',
   targetDir: '/web/pages/project'
};
```
