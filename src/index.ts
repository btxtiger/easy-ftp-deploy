#!/usr/bin/env node

import minimist from 'minimist';
import { Log } from './log';
import { exec } from 'child_process';

const configFilePath = process.cwd() + '/easy-ftp-deploy.js';
const config = require(configFilePath);

const log = new Log();
log.showIntro();

const args = minimist(process.argv.slice(2));

const configArg = Buffer.from(JSON.stringify(config)).toString('base64');

exec('php -f ./lib/php/index.php ' + configArg, (error, stdout, stdError) => {
   if (error) {
      log.error('FTP script execution failed!');
   }

   log.log('\n' + stdout);
   log.error(stdError);
});
