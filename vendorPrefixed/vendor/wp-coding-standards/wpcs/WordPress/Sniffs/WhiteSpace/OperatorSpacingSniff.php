<?php

/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace BitPayVendor\WordPressCS\WordPress\Sniffs\WhiteSpace;

use BitPayVendor\PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff as PHPCS_Squiz_OperatorSpacingSniff;
use BitPayVendor\PHP_CodeSniffer\Util\Tokens;
/**
 * Verify operator spacing, uses the Squiz sniff, but additionally also sniffs for the
 * `!` (boolean not) and the boolean and logical and/or operators.
 *
 * "Always put spaces after commas, and on both sides of logical, comparison, string and assignment operators."
 *
 * @link    https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.3.0  This sniff now has the ability to fix the issues it flags.
 * @since   0.12.0 This sniff used to be a copy of a very old and outdated version of the
 *                 upstream sniff.
 *                 Now, the sniff defers completely to the upstream sniff, adding just the
 *                 T_BOOLEAN_NOT and the logical operators (`&&` and the like) - via the
 *                 registration method and changing the value of the customizable
 *                 $ignoreNewlines property.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * Last verified with base class July 2020 at commit a957a73e3533353451eb9fd62ee58bd0aba2773c.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php
 */
final class OperatorSpacingSniff extends PHPCS_Squiz_OperatorSpacingSniff
{
    /**
     * Allow newlines instead of spaces.
     *
     * N.B.: The upstream sniff defaults to `false`.
     *
     * @var boolean
     */
    public $ignoreNewlines = \true;
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens = parent::register();
        $tokens[\T_BOOLEAN_NOT] = \T_BOOLEAN_NOT;
        $tokens += Tokens::$booleanOperators;
        return $tokens;
    }
}
