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

namespace PrestaShop\Module\AutoUpgrade\Controller;

use PrestaShop\Module\AutoUpgrade\Router\Routes;
use PrestaShop\Module\AutoUpgrade\Task\TaskName;
use PrestaShop\Module\AutoUpgrade\Task\TaskType;
use PrestaShop\Module\AutoUpgrade\Twig\PageSelectors;
use PrestaShop\Module\AutoUpgrade\Twig\Steps\Stepper;
use PrestaShop\Module\AutoUpgrade\Twig\Steps\UpdateSteps;

class UpdatePageBackupController extends AbstractPageWithStepController
{
    const CURRENT_STEP = UpdateSteps::STEP_BACKUP;

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        return $this->redirectTo(Routes::UPDATE_PAGE_BACKUP_OPTIONS);
    }

    protected function getPageTemplate(): string
    {
        return self::CURRENT_STEP;
    }

    protected function getStepTemplate(): string
    {
        // Different from self::CURRENT_STEP, because a refresh should return to the backup options.
        return 'backup';
    }

    protected function displayRouteInUrl(): ?string
    {
        return Routes::UPDATE_PAGE_BACKUP;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    protected function getParams(): array
    {
        $updateSteps = new Stepper($this->upgradeContainer->getTranslator(), TaskType::TASK_TYPE_UPDATE);

        return array_merge(
            $updateSteps->getStepParams($this::CURRENT_STEP),
            [
                'success_route' => Routes::UPDATE_STEP_BACKUP_OPTIONS,
                'submit_skip_backup_route' => Routes::UPDATE_STEP_BACKUP_SUBMIT_UPDATE,
                'download_logs_route' => Routes::DOWNLOAD_LOGS,
                'download_logs_type' => TaskType::TASK_TYPE_BACKUP,
                'retry_route' => Routes::UPDATE_PAGE_BACKUP_OPTIONS,
                'submit_error_report_route' => Routes::DISPLAY_ERROR_REPORT_MODAL,
                'initial_process_action' => TaskName::TASK_BACKUP_INITIALIZATION,
                'download_logs_parent_id' => PageSelectors::DOWNLOAD_LOGS_PARENT_ID,
            ]
        );
    }
}
