<?php

namespace App\Domain\Practice\Ports;

use App\Domain\Practice\Entities\PracticeSettings;

interface PracticeSettingsRepository
{
    public function get(): PracticeSettings;
}

