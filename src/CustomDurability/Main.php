<?php

namespace CustomDurability;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $armorInventory = $entity->getArmorInventory();
            foreach ($armorInventory->getContents() as $slot => $item) {
                if ($item instanceof Armor) {
                    $type = $item->getArmorType();
                    $customDurability = $this->config->get($type . "_durability", 100);
                    $newDurability = max(0, $item->getMaxDurability() - $event->getFinalDamage() * $customDurability);
                    $item->setDamage($newDurability);
                    $armorInventory->setItem($slot, $item);
                }
            }
        }
    }
}
