<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class CompanyPool extends BasePool
{
    protected static array $names = [
        'TechNova','Greenfield','BluePeak','BrightPath',
        'Silverline','PrimeCore','QuantumEdge','UrbanAxis'
    ];

    protected static array $suffixes = [
        'Ltd','PLC','Inc','LLC','Corp','S.A.'
    ];

    public static function name(): string
    {
        return self::randomFrom(self::$names) . ' ' .
               self::randomFrom(self::$suffixes);
    }
}