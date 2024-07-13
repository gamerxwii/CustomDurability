<?php

namespace CustomDurability;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $config;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $armorInventory = $entity->getArmorInventory();
            foreach ($armorInventory->getContents() as $slot => $item) {
                if ($item instanceof Armor) {
                    $type = $item->getArmorType();
                    // Récupérer la durabilité personnalisée depuis le config.yml
                    $customDurability = $this->config->get($type . "_durability", 100); // Valeur par défaut : 100
                    // Ajuster la durabilité en fonction des dégâts
                    $newDurability = max(0, $item->getMaxDurability() - $event->getFinalDamage() * $customDurability);
                    $item->setDamage($newDurability);
                    $armorInventory->setItem($slot, $item);
                }
            }
        }
    }
}
