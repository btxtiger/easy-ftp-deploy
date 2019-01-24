"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var chalk_1 = __importDefault(require("chalk"));
var figlet_1 = __importDefault(require("figlet"));
/**
 * Log functions
 */
var Log = /** @class */ (function () {
    function Log() {
        this.logFn = chalk_1.default.bold;
        this.warnFn = chalk_1.default.bold.yellow;
        this.errorFn = chalk_1.default.bold.red;
    }
    /**
     * Log
     */
    Log.prototype.log = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        console.log(this.logFn.apply(this, args));
    };
    /**
     * Log warn
     */
    Log.prototype.warn = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        console.log(this.warnFn.apply(this, args));
    };
    /**
     * Log error
     */
    Log.prototype.error = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        console.log(this.errorFn.apply(this, args));
    };
    /**
     * Show intro
     */
    Log.prototype.showIntro = function () {
        console.log(chalk_1.default.cyanBright(figlet_1.default.textSync('FTP  Deploy', { horizontalLayout: 'full' })));
    };
    return Log;
}());
exports.Log = Log;
