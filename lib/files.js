"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var fs_1 = __importDefault(require("fs"));
var path_1 = __importDefault(require("path"));
/**
 * Get work dir
 */
function getCurrentDirectoryBase() {
    return path_1.default.basename(process.cwd());
}
exports.getCurrentDirectoryBase = getCurrentDirectoryBase;
/**
 * Check if dir exists
 * @param filePath
 */
function directoryExists(filePath) {
    try {
        return fs_1.default.statSync(filePath).isDirectory();
    }
    catch (e) {
        return false;
    }
}
exports.directoryExists = directoryExists;
