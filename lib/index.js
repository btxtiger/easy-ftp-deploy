#!/usr/bin/env node
"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var minimist_1 = __importDefault(require("minimist"));
var log_1 = require("./log");
var child_process_1 = require("child_process");
var configFilePath = process.cwd() + '/easy-ftp-deploy.js';
var config = require(configFilePath);
var log = new log_1.Log();
log.showIntro();
var args = minimist_1.default(process.argv.slice(2));
var configArg = Buffer.from(JSON.stringify(config)).toString('base64');
child_process_1.exec('php -f ./lib/php/index.php ' + configArg, function (error, stdout, stdError) {
    if (error) {
        log.error('FTP script execution failed!');
    }
    log.log('\n' + stdout);
    log.error(stdError);
});
