import chalk from 'chalk';
import figlet from 'figlet';

/**
 * Log functions
 */
export class Log {
   private readonly logFn: Function;
   private readonly warnFn: Function;
   private readonly errorFn: Function;

   constructor() {
      this.logFn = chalk.bold;
      this.warnFn = chalk.bold.yellow;
      this.errorFn = chalk.bold.red;
   }

   /**
    * Log
    */
   public log(...args: any): void {
      console.log(this.logFn(...args));
   }

   /**
    * Log warn
    */
   public warn(...args: any): void {
      console.log(this.warnFn(...args));
   }

   /**
    * Log error
    */
   public error(...args: any): void {
      console.log(this.errorFn(...args));
   }

   /**
    * Show intro
    */
   public showIntro(): void {
      console.log(chalk.cyanBright(figlet.textSync('FTP  Deploy', { horizontalLayout: 'full' })));
   }
}
