<?php

namespace App\Domain\Billing;

enum PlanKey: string
{
    case BASIC = 'BASIC';
    case PRO = 'PRO';
}

