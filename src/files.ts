import fs from 'fs';
import path from 'path';

/**
 * Get work dir
 */
export function getCurrentDirectoryBase(): string {
   return path.basename(process.cwd());
}

/**
 * Check if dir exists
 * @param filePath
 */
export function directoryExists(filePath: string): boolean {
   try {
      return fs.statSync(filePath).isDirectory();
   } catch (e) {
      return false;
   }
}
