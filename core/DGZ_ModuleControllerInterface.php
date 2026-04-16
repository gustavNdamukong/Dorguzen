<?php

namespace Dorguzen\Core;

/**
 * Contract that every module's entry controller must fulfil.
 *
 * The $controllers property on the implementing class lists every
 * additional controller class name (without namespace) that lives
 * inside that module's controllers/ directory. The DGZ router calls
 * getControllers() during dynamic route discovery to determine whether
 * a URL segment after the module name refers to a sub-controller or
 * to a method on the module's default controller.
 *
 * Example:
 *   URL /seo/analytics/report
 *   → router asks SeoController::getControllers()
 *   → finds 'AnalyticsController' in the list
 *   → routes to AnalyticsController::report()
 */
interface DGZ_ModuleControllerInterface
{
    /**
     * Return the list of controller class names registered in this module.
     * Each entry should be the bare class name including the 'Controller'
     * suffix, e.g. 'AnalyticsController'.
     *
     * @return array<string>
     */
    public function getControllers(): array;
}
