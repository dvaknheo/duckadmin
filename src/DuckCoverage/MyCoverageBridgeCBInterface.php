<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckCoverage;

interface MyCoverageBridgeCBInterface
{
    public static function BeforeReplayTest();
    public static function GetList();
    public static function AfterReplayTest();
    public static function OnReport();
}