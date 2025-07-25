<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\AutoUpgrade\Log;

/**
 * This class reimplement the old properties of the class AdminSelfUpgrade,
 * where all the mesages were stored.
 */
class WebLogger extends Logger
{
    /** @var string[] */
    protected $normalMessages = [];

    /** @var ?string */
    protected $lastInfo;

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getLogs(): array
    {
        return $this->normalMessages;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastInfo(): ?string
    {
        return $this->lastInfo;
    }

    private function formatLog(int $level, string $message): string
    {
        return self::$levels[$level] . ' - ' . $message;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<mixed> $context
     */
    public function log($level, string $message, array $context = []): void
    {
        if (empty($message)) {
            return;
        }

        $message = $this->cleanFromSensitiveData($message);
        parent::log($level, $message, $context);

        if ($level === self::INFO) {
            $this->lastInfo = $message;
        }

        $log = $this->formatLog($level, $message);

        $this->normalMessages[] = $log;
    }
}
