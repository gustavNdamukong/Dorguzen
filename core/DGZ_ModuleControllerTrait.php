<?php

namespace Dorguzen\Core;

/**
 * Default implementation of DGZ_ModuleControllerInterface.
 *
 * Use this trait in your module's entry controller so you don't have to
 * write getControllers() by hand. Just declare the $controllers property:
 *
 *   use DGZ_ModuleControllerTrait;
 *
 *   protected array $controllers = [
 *       'AnalyticsController',
 *       'ReportsController',
 *   ];
 *
 * If you prefer to implement getControllers() yourself (as SeoController
 * does), you can implement DGZ_ModuleControllerInterface directly without
 * using this trait.
 */
trait DGZ_ModuleControllerTrait
{
    public function getControllers(): array
    {
        return $this->controllers ?? [];
    }
}
