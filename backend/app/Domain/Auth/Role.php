<?php

namespace App\Domain\Auth;

enum Role: string
{
    case TENANT_ADMIN = 'TENANT_ADMIN';
    case DOCTOR = 'DOCTOR';
    case RECEPTIONIST = 'RECEPTIONIST';
    case PATIENT_PORTAL = 'PATIENT_PORTAL';
}

