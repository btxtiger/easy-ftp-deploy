/**
 * Log functions
 */
export declare class Log {
    private readonly logFn;
    private readonly warnFn;
    private readonly errorFn;
    constructor();
    /**
     * Log
     */
    log(...args: any): void;
    /**
     * Log warn
     */
    warn(...args: any): void;
    /**
     * Log error
     */
    error(...args: any): void;
    /**
     * Show intro
     */
    showIntro(): void;
}
