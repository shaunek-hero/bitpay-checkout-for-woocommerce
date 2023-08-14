<?php

/**
 * Runs gjslint on the file.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace BitPayVendor\PHP_CodeSniffer\Standards\Generic\Sniffs\Debug;

use BitPayVendor\PHP_CodeSniffer\Config;
use BitPayVendor\PHP_CodeSniffer\Files\File;
use BitPayVendor\PHP_CodeSniffer\Sniffs\Sniff;
use BitPayVendor\PHP_CodeSniffer\Util\Common;
class ClosureLinterSniff implements Sniff
{
    /**
     * A list of error codes that should show errors.
     *
     * All other error codes will show warnings.
     *
     * @var integer
     */
    public $errorCodes = [];
    /**
     * A list of error codes to ignore.
     *
     * @var integer
     */
    public $ignoreCodes = [];
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['JS'];
    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [\T_OPEN_TAG];
    }
    //end register()
    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If jslint.js could not be run
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $lintPath = Config::getExecutablePath('gjslint');
        if ($lintPath === null) {
            return;
        }
        $fileName = $phpcsFile->getFilename();
        $lintPath = Common::escapeshellcmd($lintPath);
        $cmd = $lintPath . ' --nosummary --notime --unix_mode ' . \escapeshellarg($fileName);
        \exec($cmd, $output, $retval);
        if (\is_array($output) === \false) {
            return;
        }
        foreach ($output as $finding) {
            $matches = [];
            $numMatches = \preg_match('/^(.*):([0-9]+):\\(.*?([0-9]+)\\)(.*)$/', $finding, $matches);
            if ($numMatches === 0) {
                continue;
            }
            // Skip error codes we are ignoring.
            $code = $matches[3];
            if (\in_array($code, $this->ignoreCodes) === \true) {
                continue;
            }
            $line = (int) $matches[2];
            $error = \trim($matches[4]);
            $message = 'gjslint says: (%s) %s';
            $data = [$code, $error];
            if (\in_array($code, $this->errorCodes) === \true) {
                $phpcsFile->addErrorOnLine($message, $line, 'ExternalToolError', $data);
            } else {
                $phpcsFile->addWarningOnLine($message, $line, 'ExternalTool', $data);
            }
        }
        //end foreach
        // Ignore the rest of the file.
        return $phpcsFile->numTokens + 1;
    }
    //end process()
}
//end class
