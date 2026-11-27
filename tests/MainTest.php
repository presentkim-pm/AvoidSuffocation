<?php

declare(strict_types=1);

namespace kim\present\avoidsuffocation\tests;

use kim\present\avoidsuffocation\Main;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\utils\Utils;
use PHPUnit\Framework\TestCase;

/**
 * The server registers listener methods by reflection, so check that the handler still matches what it accepts
 * and that it runs last (HIGHEST), after other plugins have seen the damage.
 */
final class MainTest extends TestCase{

    public function testIsListener() : void{
        self::assertTrue(is_subclass_of(Main::class, Listener::class));
    }

    public function testHandlerSignature() : void{
        $reflection = new \ReflectionMethod(Main::class, 'onEntityDamageEvent');
        self::assertTrue($reflection->isPublic());
        self::assertSame('void', (string) $reflection->getReturnType());

        $params = $reflection->getParameters();
        self::assertCount(1, $params);
        self::assertSame(EntityDamageEvent::class, (string) $params[0]->getType());
    }

    public function testHandlerPriority() : void{
        $notes = Utils::parseDocComment((string) (new \ReflectionMethod(Main::class, 'onEntityDamageEvent'))->getDocComment());
        self::assertSame(EventPriority::HIGHEST, EventPriority::fromString($notes['priority'] ?? ''));
    }
}
